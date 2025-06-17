<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmplacementRequest;
use App\Http\Requests\UpdateEmplacementRequest;
use App\Models\Emplacement;

/**
 * Contrôleur pour la gestion des emplacements de stockage des articles.
 * Gère les opérations CRUD pour les emplacements.
 */
class EmplacementController extends Controller
{
    /**
     * Affiche une liste de tous les emplacements.
     *
     * @return \Illuminate\View\View La vue listant les emplacements.
     */
    public function index(): \Illuminate\View\View
    {
        return view('emplacements.index', [
            'emplacements' => Emplacement::all(), // Récupère tous les emplacements.
        ]);
    }

    /**
     * Affiche le formulaire de création d'un nouvel emplacement.
     *
     * @return \Illuminate\View\View La vue du formulaire de création.
     */
    public function create(): \Illuminate\View\View
    {
        return view('emplacements.create');
    }

    /**
     * Enregistre un nouvel emplacement dans la base de données.
     *
     * @param  \App\Http\Requests\StoreEmplacementRequest  $request La requête validée contenant les données de l'emplacement.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des emplacements avec un message de succès.
     */
    public function store(StoreEmplacementRequest $request): \Illuminate\Http\RedirectResponse
    {
        // Il est préférable de déplacer toute la validation dans StoreEmplacementRequest.
        // Pour l'instant, la validation est dupliquée ici pour s'assurer qu'elle est présente.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:emplacements,name', // Nom requis, unique.
            'description' => 'nullable|string|max:255', // Description optionnelle.
        ]);

        Emplacement::create($validatedData); // Crée l'emplacement.

        return redirect()->route('emplacements.index')
            ->with('success', 'Emplacement créé avec succès.');
    }

    /**
     * Affiche les détails d'un emplacement spécifique.
     *
     * @param  \App\Models\Emplacement  $emplacement L'emplacement à afficher.
     * @return \Illuminate\View\View La vue affichant les détails de l'emplacement.
     */
    public function show(Emplacement $emplacement): \Illuminate\View\View
    {
        return view('emplacements.show', compact('emplacement')); // Passe l'emplacement à la vue.
    }

    /**
     * Affiche le formulaire de modification d'un emplacement existant.
     *
     * @param  \App\Models\Emplacement  $emplacement L'emplacement à modifier.
     * @return \Illuminate\View\View La vue du formulaire de modification.
     */
    public function edit(Emplacement $emplacement): \Illuminate\View\View
    {
        return view('emplacements.edit', compact('emplacement')); // Passe l'emplacement à la vue.
    }

    /**
     * Met à jour un emplacement spécifique dans la base de données.
     *
     * @param  \App\Http\Requests\UpdateEmplacementRequest  $request La requête validée contenant les données de mise à jour.
     * @param  \App\Models\Emplacement  $emplacement L'emplacement à mettre à jour.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des emplacements avec un message de succès.
     */
    public function update(UpdateEmplacementRequest $request, Emplacement $emplacement): \Illuminate\Http\RedirectResponse
    {
        // Il est préférable de déplacer toute la validation dans UpdateEmplacementRequest.
        $validatedData = $request->validate([
            // Nom requis, unique, sauf pour l'enregistrement actuel.
            'name' => 'required|string|max:255|unique:emplacements,name,' . $emplacement->id,
            'description' => 'nullable|string|max:255', // Description optionnelle.
        ]);

        $emplacement->update($validatedData); // Met à jour l'emplacement.

        return redirect()->route('emplacements.index')
            ->with('success', 'Emplacement mis à jour avec succès.');
    }

    /**
     * Supprime un emplacement spécifique de la base de données.
     * Empêche la suppression si l'emplacement est associé à des articles.
     *
     * @param  \App\Models\Emplacement  $emplacement L'emplacement à supprimer.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des emplacements avec un message de succès ou d'erreur.
     */
    public function destroy(Emplacement $emplacement): \Illuminate\Http\RedirectResponse
    {
        // Vérifie si l'emplacement est associé à des articles.
        if ($emplacement->articles()->count() > 0) {
            return redirect()->route('emplacements.index')
                ->with('error', 'Impossible de supprimer l\'emplacement car il est associé à des articles.');
        }
        $emplacement->delete(); // Supprime l'emplacement.

        return redirect()->route('emplacements.index')
            ->with('success', 'Emplacement supprimé avec succès.');
    }
}
