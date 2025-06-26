<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Requests\StoreFactureRequest;
use App\Http\Requests\UpdateFactureRequest;
use App\Notifications\StockLowNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

/**
 * Contrôleur pour la gestion des factures.
 * Gère les opérations CRUD pour les factures, la génération de PDF,
 * et la vérification des stocks lors de la création/mise à jour.
 */
class FactureController extends Controller
{
    /**
     * Affiche une liste paginée des factures avec une fonctionnalité de recherche.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP, peut contenir un terme de recherche.
     * @return \Illuminate\View\View La vue listant les factures.
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $query = Facture::query(); // Initialise une requête Eloquent pour les factures.

        // Applique un filtre de recherche si un terme est fourni.
        if ($request->has('search')) {
            $search = $request->input('search');
            // Recherche par ID de facture ou par statut de paiement.
            $query->where('id', 'like', "%{$search}%")
                  ->orWhere('statut_paiement', 'like', "%{$search}%");
        }

        $factures = $query->paginate(15); // Pagine les résultats.

        return view('factures.index', compact('factures'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle facture.
     *
     * @return \Illuminate\View\View La vue du formulaire de création avec la liste des articles.
     */
    public function create(): \Illuminate\View\View
    {
        return view('factures.create', [
            'articles' => Article::all(), // Fournit tous les articles pour la sélection.
        ]);
    }

    /**
     * Enregistre une nouvelle facture dans la base de données.
     * Calcule les montants, décrémente les stocks, et génère un PDF de la facture.
     *
     * @param  \App\Http\Requests\StoreFactureRequest  $request La requête HTTP validée contenant les données de la facture.
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse Le PDF de la facture ou une redirection en cas d'erreur.
     */
    public function store(StoreFactureRequest $request) // Le type de retour peut être Response pour le PDF.
    {
        $validated = $request->validated();

        DB::beginTransaction(); // Démarre une transaction de base de données.

        try {
            // $articlesData = $validated['articles']; // Déjà inclus dans $validated
            // Pour la clarté, on peut extraire :
            $articlesData = $validated['articles'];
            $montantHTTotal = 0;
            $details = []; // Pour stocker les détails des lignes de facture pour le PDF.

            // Vérifie le stock et calcule les montants pour chaque article.
            foreach ($articlesData as $index => $item) {
                $article = Article::findOrFail($item['article_id']); // Trouve l'article ou échoue.

                // Vérifie si la quantité demandée est disponible en stock.
                if ($item['quantity'] > $article->quantite) {
                    DB::rollBack(); // Annule la transaction.
                    return redirect()->back()
                        ->withErrors(["articles.{$index}.quantity" => "Quantité en stock insuffisante pour l'article {$article->name}."])
                        ->withInput(); // Retourne avec une erreur spécifique à la ligne d'article.
                }

                $prixUnitaire = $article->prix;
                $ligneHT = $prixUnitaire * $item['quantity'];

                $details[] = [
                    'article' => $article,
                    'quantity' => $item['quantity'],
                    'prix_unitaire' => $prixUnitaire,
                    'montant_ht' => $ligneHT,
                ];

                $montantHTTotal += $ligneHT; // Ajoute au montant total hors taxes.
            }

            $tva = 18; // Taux de TVA (ex: 18%).
            $montantTTC = $montantHTTotal * (1 + $tva / 100); // Calcule le montant toutes taxes comprises.

            // Génère un numéro de facture unique.
            $numero = 'FAC-' . date('Y') . '-' . str_pad(Facture::withTrashed()->count() + 1, 4, '0', STR_PAD_LEFT);

            // Crée la facture.
            $facture = Facture::create([
                'client_nom' => $validated['client_nom'],
                'client_prenom' => $validated['client_prenom'],
                'client_adresse' => $validated['client_adresse'],
                'client_telephone' => $validated['client_telephone'],
                'client_email' => $validated['client_email'],
                'numero' => $numero, // Inclut le numéro de facture.
                'date_facture' => now(),
                'montant_ht' => $montantHTTotal,
                'tva' => $tva,
                'montant_ttc' => $montantTTC,
                'mode_paiement' => $validated['mode_paiement'],
                'statut_paiement' => $validated['statut_paiement'],
                'date_paiement' => $validated['statut_paiement'] === 'payé' ? now() : null,
            ]);

            // Attache les articles à la facture et décrémente les stocks.
            foreach ($details as $d) {
                $facture->articles()->attach($d['article']->id, [
                    'quantite' => $d['quantity'],
                    'prix_unitaire' => $d['prix_unitaire'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $d['article']->decrement('quantite', $d['quantity']); // Décrémente le stock de l'article.

                // Envoie une notification si le stock de l'article est bas.
                if ($d['article']->fresh()->quantite < 5) { // 'fresh()' pour obtenir la dernière valeur du stock.
                    Notification::send(User::role('admin')->get(), new StockLowNotification($d['article'], 5)); // Notifie les admins.
                }
            }

            // Génère le PDF de la facture.
            $pdf = Pdf::loadView('factures.pdf', [
                'facture' => $facture,
                'details' => $details, // Passe les détails calculés à la vue PDF.
            ]);

            DB::commit(); // Valide la transaction.
            return $pdf->download("Facture_{$facture->numero}.pdf"); // Télécharge le PDF.

        } catch (ModelNotFoundException $e) {
            DB::rollBack(); // Annule la transaction en cas d'erreur.
            Log::error('Article non trouvé lors de la création de la facture.', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['article_id' => 'Un article sélectionné est introuvable.'])->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la facture : ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withErrors(['general' => 'Une erreur est survenue lors de la création de la facture : ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Affiche les détails d'une facture spécifique.
     *
     * @param  \App\Models\Facture  $facture La facture à afficher.
     * @return \Illuminate\View\View La vue affichant les détails de la facture.
     */
    public function show(Facture $facture): \Illuminate\View\View
    {
        return view('factures.show', compact('facture')); // Passe la facture à la vue.
    }

    /**
     * Affiche le formulaire de modification d'une facture existante.
     *
     * @param  \App\Models\Facture  $facture La facture à modifier.
     * @return \Illuminate\View\View La vue du formulaire de modification.
     */
    public function edit(Facture $facture): \Illuminate\View\View
    {
        return view('factures.edit', [
            'facture' => $facture,
            'articles' => Article::all(), // Fournit tous les articles pour la sélection.
        ]);
    }

    /**
     * Met à jour une facture spécifique dans la base de données.
     * Recalcule les montants et met à jour les stocks si les articles sont modifiés.
     *
     * @param  \App\Http\Requests\UpdateFactureRequest  $request La requête HTTP validée contenant les données de mise à jour.
     * @param  \App\Models\Facture  $facture La facture à mettre à jour.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des factures avec un message de succès ou d'erreur.
     */
    public function update(UpdateFactureRequest $request, Facture $facture): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction(); // Démarre une transaction.

        try {
            $montantHTTotal = 0;
            $details = []; // Pour stocker les nouveaux détails des lignes de facture.

            // Si des articles sont fournis, traite-les (potentiellement pour remplacer les anciens).
            if (!empty($validated['articles'])) {
                $articlesData = $validated['articles'];
                 // Annuler les anciennes quantités d'articles avant la mise à jour
                foreach ($facture->articles as $articlePivot) {
                    $articleModel = Article::find($articlePivot->id);
                    if ($articleModel) {
                        $articleModel->increment('quantite', $articlePivot->pivot->quantite);
                    }
                }


                foreach ($articlesData as $index => $item) {
                    $article = Article::findOrFail($item['article_id']);

                    // Vérifie le stock disponible (en tenant compte de la quantité déjà sur la facture si l'article n'est pas nouveau).
                    $quantiteActuelleFacture = 0; // À implémenter si on veut permettre l'édition fine des lignes existantes.
                                                // Pour cette version, on suppose que les articles sont remplacés.
                    if ($item['quantity'] > ($article->quantite + $quantiteActuelleFacture)) {
                        DB::rollBack();
                        return redirect()->back()
                            ->withErrors(["articles.{$index}.quantity" => "Quantité en stock insuffisante pour l'article {$article->name}."])
                            ->withInput();
                    }

                    $prixUnitaire = $article->prix;
                    $ligneHT = $prixUnitaire * $item['quantity'];
                    $details[] = [
                        'article' => $article,
                        'quantity' => $item['quantity'],
                        'prix_unitaire' => $prixUnitaire,
                        'montant_ht' => $ligneHT,
                    ];
                    $montantHTTotal += $ligneHT;
                }
                $tva = 18; // Taux de TVA.
                $montantTTC = $montantHTTotal * (1 + $tva / 100);
            } else {
                // Si aucun article n'est fourni, conserve les montants existants (utile si on ne met à jour que le statut).
                $montantHTTotal = $facture->montant_ht;
                $montantTTC = $facture->montant_ttc;
                $tva = $facture->tva;
            }


            // Met à jour la facture.
            $facture->update([
                'client_nom' => $validated['client_nom'] ?? $facture->client_nom,
                'client_prenom' => $validated['client_prenom'] ?? $facture->client_prenom,
                'client_adresse' => $validated['client_adresse'] ?? $facture->client_adresse,
                'client_telephone' => $validated['client_telephone'] ?? $facture->client_telephone,
                'client_email' => $validated['client_email'] ?? $facture->client_email,
                'montant_ht' => $montantHTTotal,
                'tva' => $tva,
                'montant_ttc' => $montantTTC,
                'mode_paiement' => $validated['mode_paiement'] ?? $facture->mode_paiement,
                'statut_paiement' => $validated['statut_paiement'] ?? $facture->statut_paiement,
                'date_paiement' => ($validated['statut_paiement'] ?? $facture->statut_paiement) === 'payé' ? now() : null,
            ]);

            // Si de nouveaux articles ont été fournis, détache les anciens et attache les nouveaux.
            if (!empty($validated['articles'])) {
                $facture->articles()->detach(); // Détache tous les anciens articles.
                foreach ($details as $d) {
                    $facture->articles()->attach($d['article']->id, [
                        'quantite' => $d['quantity'],
                        'prix_unitaire' => $d['prix_unitaire'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $d['article']->decrement('quantite', $d['quantity']); // Décrémente le stock.

                    // Notifie si le stock est bas.
                    if ($d['article']->fresh()->quantite < 5) {
                         Notification::send(User::role('admin')->get(), new StockLowNotification($d['article'], 5));
                    }
                }
            }

            DB::commit(); // Valide la transaction.
            return redirect()->route('factures.index')->with('success', 'Facture mise à jour avec succès.');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Article non trouvé lors de la mise à jour de la facture.', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['article_id' => 'Un article sélectionné est introuvable.'])->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de la facture : ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withErrors(['general' => 'Une erreur est survenue lors de la mise à jour : ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Supprime une facture spécifique de la base de données.
     * Note : Ne réajuste pas les stocks des articles lors de la suppression.
     *
     * @param  \App\Models\Facture  $facture La facture à supprimer.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des factures avec un message de succès.
     */
    public function destroy(Facture $facture): \Illuminate\Http\RedirectResponse
    {
        // Implémentation de la logique pour réajuster les stocks si nécessaire avant suppression
        // foreach ($facture->articles as $articlePivot) {
        //     $articleModel = Article::find($articlePivot->id);
        //     if ($articleModel) {
        //         $articleModel->increment('quantite', $articlePivot->pivot->quantite);
        //     }
        // }
        // $facture->articles()->detach(); // Optionnel si la suppression en cascade est configurée ou si on gère manuellement.

        $facture->delete(); // Supprime la facture.
        return redirect()->route('factures.index')->with('success', 'Facture supprimée avec succès.');
    }

    /**
     * Génère et télécharge un PDF pour une facture spécifique.
     *
     * @param  \App\Models\Facture  $facture La facture pour laquelle générer le PDF.
     * @return \Symfony\Component\HttpFoundation\Response Le PDF à télécharger.
     */
    public function genererPdf(Facture $facture): \Symfony\Component\HttpFoundation\Response
    {
        // Recharge la facture avec ses articles pour s'assurer d'avoir les dernières données.
        $facture = Facture::with('articles')->findOrFail($facture->id);

        // Prépare les détails des articles de la facture pour la vue PDF.
        // Ceci est similaire à ce qui pourrait être fait dans 'store' ou 'show' si la vue PDF en a besoin.
        $details = [];
        foreach ($facture->articles as $articlePivot) {
            // Suppose que 'prix_unitaire' et 'quantite' sont stockés dans la table pivot 'article_facture'.
            $prixUnitaire = $articlePivot->pivot->prix_unitaire;
            $quantity = $articlePivot->pivot->quantite;
            $ligneHT = $prixUnitaire * $quantity;

            $details[] = [
                'article' => $articlePivot, // Le modèle Article lui-même.
                'quantity' => $quantity,
                'prix_unitaire' => $prixUnitaire,
                'montant_ht' => $ligneHT,
            ];
        }

        // La vue PDF peut aussi s'attendre à des totaux calculés directement depuis le modèle $facture
        // s'ils sont stockés dans la table des factures (ex: $facture->montant_ht, $facture->montant_ttc).
        // Ajuster les données passées à la vue en fonction de ce que `factures.pdf.blade.php` attend.

        $pdf = Pdf::loadView('factures.pdf', [
            'facture' => $facture,
            'details' => $details, // Passer ceci si votre vue PDF itère sur 'details'.
                                // Sinon, si elle utilise $facture->articles directement, s'assurer que cette relation est correctement structurée.
        ]);

        // Télécharge le PDF avec un nom de fichier dynamique.
        return $pdf->download('Facture_' . ($facture->numero ? $facture->numero : $facture->id) . '.pdf');
    }
}
