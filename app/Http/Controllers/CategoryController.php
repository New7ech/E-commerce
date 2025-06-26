<?php

namespace App\Http\Controllers;

// Mise à jour des imports pour utiliser les nouveaux noms de FormRequest et le modèle Category
use App\Http\Requests\StoreCategoryRequest; // Sera renommé/créé
use App\Http\Requests\UpdateCategoryRequest; // Sera renommé/créé
use App\Models\Category; // Utilisation du modèle Category consolidé
use App\Models\Article; // Utilisé dans showPublic

/**
 * Contrôleur pour la gestion des catégories d'articles.
 * Gère les opérations CRUD pour les catégories.
 */
class CategoryController extends Controller // Nom de classe mis à jour
{
    /**
     * Affiche une liste de toutes les catégories.
     *
     * @return \Illuminate\View\View La vue listant les catégories.
     */
    public function index(): \Illuminate\View\View
    {
        return view('categories.index', [ // Le chemin de la vue reste categories.index pour l'instant
            'categories' => Category::all(), // Utilise le modèle Category
        ]);
    }

    /**
     * Affiche le formulaire de création d'une nouvelle catégorie.
     *
     * @return \Illuminate\View\View La vue du formulaire de création.
     */
    public function create(): \Illuminate\View\View
    {
        return view('categories.create'); // Le chemin de la vue reste categories.create
    }

    /**
     * Enregistre une nouvelle catégorie dans la base de données.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request La requête validée contenant les données de la catégorie.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des catégories avec un message de succès.
     */
    public function store(StoreCategoryRequest $request): \Illuminate\Http\RedirectResponse // Utilise StoreCategoryRequest
    {
        $validatedData = $request->validated();

        // Crée la catégorie avec le nom et la description validés.
        // S'assure que le slug est généré si non fourni explicitement par le FormRequest.
        // Souvent, le slug est généré à partir du nom.
        if (empty($validatedData['slug']) && !empty($validatedData['name'])) {
            $validatedData['slug'] = \Illuminate\Support\Str::slug($validatedData['name']);
        }

        Category::create($validatedData); // Utilise le modèle Category

        return redirect()->route('admin.categories.index') // Mise à jour du nom de la route si elle change avec le préfixe admin
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Affiche les détails d'une catégorie spécifique.
     *
     * @param  \App\Models\Category  $category La catégorie à afficher.
     * @return \Illuminate\View\View
     */
    public function show(Category $category): \Illuminate\View\View // Utilise le modèle Category
    {
        // Cette méthode peut être utilisée pour une page de détails admin si nécessaire.
        // Ou pour une prévisualisation.
        return view('categories.show', compact('category')); // Le chemin de la vue reste categories.show
    }

    /**
     * Affiche le formulaire de modification d'une catégorie existante.
     *
     * @param  \App\Models\Category  $category La catégorie à modifier.
     * @return \Illuminate\View\View La vue du formulaire de modification.
     */
    public function edit(Category $category): \Illuminate\View\View // Utilise le modèle Category
    {
        return view('categories.edit', compact('category')); // Le chemin de la vue reste categories.edit
    }

    /**
     * Met à jour une catégorie spécifique dans la base de données.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request La requête validée contenant les données de mise à jour.
     * @param  \App\Models\Category  $category La catégorie à mettre à jour.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des catégories avec un message de succès.
     */
    public function update(UpdateCategoryRequest $request, Category $category): \Illuminate\Http\RedirectResponse // Utilise UpdateCategoryRequest et Category
    {
        $validatedData = $request->validated();

        // Si le nom est mis à jour et que le slug n'est pas fourni, régénérer le slug.
        if (empty($validatedData['slug']) && $category->name !== $validatedData['name']) {
            $validatedData['slug'] = \Illuminate\Support\Str::slug($validatedData['name']);
        }

        $category->update($validatedData);

        return redirect()->route('admin.categories.index') // Mise à jour du nom de la route
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprime une catégorie spécifique de la base de données.
     * Empêche la suppression si la catégorie est associée à des articles.
     *
     * @param  \App\Models\Category  $category La catégorie à supprimer.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des catégories avec un message de succès ou d'erreur.
     */
    public function destroy(Category $category): \Illuminate\Http\RedirectResponse // Utilise le modèle Category
    {
        if ($category->articles()->count() > 0) {
            return redirect()->route('admin.categories.index') // Mise à jour du nom de la route
                ->with('error', 'Impossible de supprimer la catégorie car elle est associée à des articles.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index') // Mise à jour du nom de la route
            ->with('success', 'Catégorie supprimée avec succès.');
    }

    /**
     * Affiche les produits d'une catégorie spécifique pour le public.
     *
     * @param  \App\Models\Category  $category La catégorie à afficher (model binding avec le slug).
     * @return \Illuminate\View\View
     */
    public function showPublic(Category $category): \Illuminate\View\View // Utilise le modèle Category
    {
        $articles = $category->articles()->paginate(12);
        $categories = Category::all(); // Utilise le modèle Category pour la liste des catégories

        return view('categories.show-public', compact('category', 'articles', 'categories'));
    }
}
