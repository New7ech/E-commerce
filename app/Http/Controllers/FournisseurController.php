<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFournisseurRequest;
use App\Http\Requests\UpdateFournisseurRequest;
use App\Models\Fournisseur;

/**
 * Contrôleur pour la gestion des fournisseurs.
 * Gère les opérations CRUD pour les fournisseurs.
 */
class FournisseurController extends Controller
{
    /**
     * Affiche une liste de tous les fournisseurs.
     *
     * @return \Illuminate\View\View La vue listant les fournisseurs.
     */
    public function index(): \Illuminate\View\View
    {
        return view('fournisseurs.index', [
            'fournisseurs' => Fournisseur::all(), // Récupère tous les fournisseurs.
        ]);
    }

    /**
     * Affiche le formulaire de création d'un nouveau fournisseur.
     *
     * @return \Illuminate\View\View La vue du formulaire de création.
     */
    public function create(): \Illuminate\View\View
    {
        return view('fournisseurs.create');
    }

    /**
     * Enregistre un nouveau fournisseur dans la base de données.
     *
     * @param  \App\Http\Requests\StoreFournisseurRequest  $request La requête validée contenant les données du fournisseur.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des fournisseurs avec un message de succès.
     */
    public function store(StoreFournisseurRequest $request): \Illuminate\Http\RedirectResponse
    {
        // Il est préférable de déplacer toute la validation dans StoreFournisseurRequest.
        // Pour l'instant, la validation est ici pour s'assurer qu'elle est présente.
        // $request->validated() devrait être utilisé si StoreFournisseurRequest gère toutes les règles.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:fournisseurs,name', // Nom du contact/représentant du fournisseur
            'description' => 'nullable|string', // Description optionnelle du fournisseur
            'nom_entreprise' => 'required|string|max:255', // Nom officiel de l'entreprise fournisseur
            'adresse' => 'required|string|max:255', // Adresse postale
            'telephone' => 'required|string|max:20', // Numéro de téléphone
            'email' => 'required|email|unique:fournisseurs,email', // Adresse e-mail, unique
            'ville' => 'required|string|max:255', // Ville
            'pays' => 'required|string|max:255', // Pays
        ]);

        Fournisseur::create($validatedData); // Crée le fournisseur.

        return redirect()->route('fournisseurs.index')
            ->with('success', 'Fournisseur créé avec succès.');
    }

    /**
     * Affiche les détails d'un fournisseur spécifique.
     *
     * @param  \App\Models\Fournisseur  $fournisseur Le fournisseur à afficher.
     * @return \Illuminate\View\View La vue affichant les détails du fournisseur.
     */
    public function show(Fournisseur $fournisseur): \Illuminate\View\View
    {
        // Généralement, 'show' n'est pas très utilisé si 'edit' couvre les détails,
        // ou si la page d'index est suffisamment détaillée.
        // Pour la cohérence, une vue simple peut être souhaitée.
        return view('fournisseurs.show', compact('fournisseur')); // Suppose qu'une vue 'show' existe ou sera créée.
    }

    /**
     * Affiche le formulaire de modification d'un fournisseur existant.
     *
     * @param  \App\Models\Fournisseur  $fournisseur Le fournisseur à modifier.
     * @return \Illuminate\View\View La vue du formulaire de modification.
     */
    public function edit(Fournisseur $fournisseur): \Illuminate\View\View
    {
        return view('fournisseurs.edit', compact('fournisseur')); // Passe le fournisseur à la vue.
    }

    /**
     * Met à jour un fournisseur spécifique dans la base de données.
     *
     * @param  \App\Http\Requests\UpdateFournisseurRequest  $request La requête validée contenant les données de mise à jour.
     * @param  \App\Models\Fournisseur  $fournisseur Le fournisseur à mettre à jour.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des fournisseurs avec un message de succès.
     */
    public function update(UpdateFournisseurRequest $request, Fournisseur $fournisseur): \Illuminate\Http\RedirectResponse
    {
        // Il est préférable de déplacer toute la validation dans UpdateFournisseurRequest.
        // $request->validated() devrait être utilisé si UpdateFournisseurRequest gère toutes les règles.
        $validatedData = $request->validate([
            // Nom unique, sauf pour l'enregistrement actuel.
            'name' => 'required|string|max:255|unique:fournisseurs,name,' . $fournisseur->id,
            'description' => 'nullable|string',
            'nom_entreprise' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            // Email unique, sauf pour l'enregistrement actuel.
            'email' => 'required|email|unique:fournisseurs,email,' . $fournisseur->id,
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255',
        ]);

        $fournisseur->update($validatedData); // Met à jour le fournisseur.

        return redirect()->route('fournisseurs.index')
            ->with('success', 'Fournisseur mis à jour avec succès.');
    }

    /**
     * Supprime un fournisseur spécifique de la base de données.
     * Empêche la suppression si le fournisseur est associé à des articles.
     *
     * @param  \App\Models\Fournisseur  $fournisseur Le fournisseur à supprimer.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des fournisseurs avec un message de succès ou d'erreur.
     */
    public function destroy(Fournisseur $fournisseur): \Illuminate\Http\RedirectResponse
    {
        // Vérifie si le fournisseur est associé à des articles.
        if ($fournisseur->articles()->count() > 0) {
            return redirect()->route('fournisseurs.index')
                ->with('error', 'Impossible de supprimer le fournisseur car il est associé à des articles.');
        }
        $fournisseur->delete(); // Supprime le fournisseur.

        return redirect()->route('fournisseurs.index')
            ->with('success', 'Fournisseur supprimé avec succès.');
    }
}
