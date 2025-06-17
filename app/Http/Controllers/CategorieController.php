<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;
use App\Models\Categorie;

/**
 * Contrôleur pour la gestion des catégories d'articles.
 * Gère les opérations CRUD pour les catégories.
 */
class CategorieController extends Controller
{
    /**
     * Affiche une liste de toutes les catégories.
     *
     * @return \Illuminate\View\View La vue listant les catégories.
     */
    public function index(): \Illuminate\View\View
    {
        return view('categories.index', [
            'categories' => Categorie::all(), // Récupère toutes les catégories.
        ]);
    }

    /**
     * Affiche le formulaire de création d'une nouvelle catégorie.
     *
     * @return \Illuminate\View\View La vue du formulaire de création.
     */
    public function create(): \Illuminate\View\View
    {
        return view('categories.create');
    }

    /**
     * Enregistre une nouvelle catégorie dans la base de données.
     *
     * @param  \App\Http\Requests\StoreCategorieRequest  $request La requête validée contenant les données de la catégorie.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des catégories avec un message de succès.
     */
    public function store(StoreCategorieRequest $request): \Illuminate\Http\RedirectResponse
    {
        // Valide les données de la requête. Le nom doit être requis et unique dans la table 'categories'.
        // La description est optionnelle, une chaîne de caractères avec un maximum de 255 caractères.
        // Note : La validation du nom est déjà dans StoreCategorieRequest, mais la description est ajoutée ici.
        // Il serait préférable de tout centraliser dans StoreCategorieRequest.
        $validatedData = $request->validate([ // $request->validated() si StoreCategorieRequest gère tout.
            'name' => 'required|unique:categories,name', // Assure que le nom est unique
            'description' => 'nullable|string|max:255',
        ]);

        // Crée la catégorie avec le nom et la description validés.
        Categorie::create($validatedData);

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie créée avec succès.'); // Message de succès traduit.
    }

    /**
     * Affiche les détails d'une catégorie spécifique.
     * (Méthode actuellement vide et non utilisée dans les routes par défaut de ressource)
     *
     * @param  \App\Models\Categorie  $categorie La catégorie à afficher.
     * @return void
     */
    public function show(Categorie $categorie)
    {
        // Cette méthode n'est généralement pas utilisée pour les catégories si seule la liste et la modification sont nécessaires.
        // Si une vue de détail est requise, elle serait implémentée ici.
    }

    /**
     * Affiche le formulaire de modification d'une catégorie existante.
     *
     * @param  \App\Models\Categorie  $categorie La catégorie à modifier.
     * @return \Illuminate\View\View La vue du formulaire de modification.
     */
    public function edit(Categorie $categorie): \Illuminate\View\View
    {
        return view('categories.edit', compact('categorie')); // Passe la catégorie à la vue.
    }

    /**
     * Met à jour une catégorie spécifique dans la base de données.
     *
     * @param  \App\Http\Requests\UpdateCategorieRequest  $request La requête validée contenant les données de mise à jour.
     * @param  \App\Models\Categorie  $categorie La catégorie à mettre à jour.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des catégories avec un message de succès.
     */
    public function update(UpdateCategorieRequest $request, Categorie $categorie): \Illuminate\Http\RedirectResponse
    {
        // Il est préférable de déplacer toute la validation dans UpdateCategorieRequest.
        // Pour l'instant, nous répliquons une logique similaire à 'store', en ajustant pour l'unicité.
        // Le nom doit être unique, sauf pour l'enregistrement actuel.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $categorie->id,
            'description' => 'nullable|string|max:255',
        ]);

        $categorie->update($validatedData); // Met à jour la catégorie avec les données validées.

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprime une catégorie spécifique de la base de données.
     * Empêche la suppression si la catégorie est associée à des articles.
     *
     * @param  \App\Models\Categorie  $categorie La catégorie à supprimer.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des catégories avec un message de succès ou d'erreur.
     */
    public function destroy(Categorie $categorie): \Illuminate\Http\RedirectResponse
    {
        // Vérifie si la catégorie est associée à des articles.
        if ($categorie->articles()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Impossible de supprimer la catégorie car elle est associée à des articles.');
        }

        $categorie->delete(); // Supprime la catégorie.

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}
