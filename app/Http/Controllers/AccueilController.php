<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Accueil;
use App\Models\Article;
use App\Models\Facture;
use App\Models\Fournisseur;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccueilRequest;
use App\Http\Requests\UpdateAccueilRequest;

/**
 * Contrôleur pour gérer la page d'accueil et afficher les statistiques globales.
 */
class AccueilController extends Controller
{
    /**
     * Affiche la page d'accueil avec diverses statistiques sur les factures.
     * Calcule et transmet à la vue des données telles que le nombre total de factures,
     * le montant total, le nombre de factures payées/impayées, etc.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer toutes les factures
        $factures = Facture::all();

        // Calculer le nombre total de factures
        $nombreFactures = $factures->count();

        // Calculer le montant total des factures pour le mois courant
        $montantTotal = $factures->filter(function ($facture) {
            $dateFacture = Carbon::parse($facture->date_facture);
            return $dateFacture->year == now()->year && $dateFacture->month == now()->month;
        })->sum('montant_ttc');

        // Calculer le nombre de factures payées
        $nombreFacturesPayees = $factures->where('statut_paiement', 'payé')->count();

        // Calculer le nombre de factures impayées
        $nombreFacturesImpayees = $factures->where('statut_paiement', 'impayé')->count();

        // Calculer le montant total des factures impayées
        $montantImpayes = $factures->where('statut_paiement', 'impayé')->sum('montant_ttc');

        // Calculer le nombre de factures pour le mois courant
        $nombreFacturesMoisCourant = $factures->filter(function ($facture) {
            $dateFacture = Carbon::parse($facture->date_facture);
            return $dateFacture->year == now()->year && $dateFacture->month == now()->month;
        })->count();

        // Calculer le montant total par mode de paiement
        $montantCarte = $factures->where('mode_paiement', 'carte')->sum('montant_ttc');
        $montantCheque = $factures->where('mode_paiement', 'chèque')->sum('montant_ttc');
        $montantEspeces = $factures->where('mode_paiement', 'espèces')->sum('montant_ttc');

        // Récupérer les factures impayées
        $facturesImpayees = $factures->where('statut_paiement', 'impayé');

        // Récupérer les factures récentes (par exemple, les 10 dernières)
        // Triées par date de facture, de la plus récente à la plus ancienne
        $facturesRecentes = $factures->sortByDesc(function ($facture) {
            return Carbon::parse($facture->date_facture);
        })->take(10);

        // Calculer les données pour le graphique d'évolution des montants impayés par mois pour l'année en cours
        $labels = [];
        $data = [];
        for ($i = 1; $i <= 12; $i++) { // Pour chaque mois de l'année
            $labels[] = ucfirst(Carbon::create()->month($i)->translatedFormat('F')); // Nom du mois en français
            $data[] = $factures->filter(function ($facture) use ($i) {
                $dateFacture = Carbon::parse($facture->date_facture);
                // Filtrer par mois et par statut 'impayé' pour l'année en cours
                return $dateFacture->year == now()->year && $dateFacture->month == $i && $facture->statut_paiement == 'impayé';
            })->sum('montant_ttc');
        }

        // Passer les variables à la vue 'welcome'
        return view('welcome', compact(
            'nombreFactures',
            'montantTotal',
            'nombreFacturesPayees',
            'nombreFacturesImpayees',
            'montantImpayes',
            'nombreFacturesMoisCourant',
            'montantCarte',
            'montantCheque',
            'montantEspeces',
            'facturesImpayees',
            'facturesRecentes',
            'labels',
            'data'
        ));
    }














    /**
     * Affiche le formulaire de création d'une nouvelle ressource Accueil.
     * (Méthode actuellement vide et non utilisée)
     *
     * @return void
     */
    public function create()
    {
        // Cette méthode n'est actuellement pas implémentée.
    }

    /**
     * Enregistre une nouvelle ressource Accueil dans la base de données.
     * (Méthode actuellement vide et non utilisée)
     *
     * @param  \App\Http\Requests\StoreAccueilRequest  $request
     * @return void
     */
    public function store(StoreAccueilRequest $request)
    {
        // Cette méthode n'est actuellement pas implémentée.
    }

    /**
     * Affiche une ressource Accueil spécifique.
     * (Méthode actuellement vide et non utilisée)
     *
     * @param  \App\Models\Accueil  $accueil
     * @return void
     */
    public function show(Accueil $accueil)
    {
        // Cette méthode n'est actuellement pas implémentée.
    }

    /**
     * Affiche le formulaire de modification d'une ressource Accueil spécifique.
     * (Méthode actuellement vide et non utilisée)
     *
     * @param  \App\Models\Accueil  $accueil
     * @return void
     */
    public function edit(Accueil $accueil)
    {
        // Cette méthode n'est actuellement pas implémentée.
    }

    /**
     * Met à jour une ressource Accueil spécifique dans la base de données.
     * (Méthode actuellement vide et non utilisée)
     *
     * @param  \App\Http\Requests\UpdateAccueilRequest  $request
     * @param  \App\Models\Accueil  $accueil
     * @return void
     */
    public function update(UpdateAccueilRequest $request, Accueil $accueil)
    {
        // Cette méthode n'est actuellement pas implémentée.
    }

    /**
     * Supprime une ressource Accueil spécifique de la base de données.
     * (Méthode actuellement vide et non utilisée)
     *
     * @param  \App\Models\Accueil  $accueil
     * @return void
     */
    public function destroy(Accueil $accueil)
    {
        // Cette méthode n'est actuellement pas implémentée.
    }
}
