<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Categorie;
use App\Models\Emplacement;
use App\Models\Fournisseur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Affiche une liste des ressources.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP.
     * @return \Illuminate\View\View La vue contenant la liste des articles.
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $search = $request->input('search');

        $articles = Article::when($search, fn($query, $term) => $query->searchByText($term))->latest()->get();

        return view('articles.index', compact('articles'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle ressource.
     *
     * @return \Illuminate\View\View La vue du formulaire de création.
     */
    public function create(): \Illuminate\View\View
    {
        $categories = Categorie::all();
        $fournisseurs = Fournisseur::all();
        $emplacements = Emplacement::all();
        $users = User::all();
        return view('articles.create', compact('categories', 'fournisseurs', 'emplacements', 'users'));
    }

    /**
     * Enregistre une nouvelle ressource dans la base de données.
     *
     * @param  \App\Http\Requests\StoreArticleRequest  $request La requête de stockage validée.
     * @return \Illuminate\Http\RedirectResponse Une redirection vers la liste des articles avec un message de succès.
     */
    public function store(StoreArticleRequest $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('articles', 'public');
            $data['image_path'] = $path;
        }

        $article = Article::create($data + ['created_by' => auth()->id()]);

        return redirect()->route('articles.index')->with('success', 'Article créé avec succès.');
    }

    /**
     * Affiche la ressource spécifiée.
     *
     * @param  \App\Models\Article  $article L'instance de l'article à afficher.
     * @return \Illuminate\View\View La vue affichant les détails de l'article.
     */
    public function show(Article $article): \Illuminate\View\View
    {
        return view('articles.show', compact('article'));
    }

    /**
     * Affiche le formulaire de modification de la ressource spécifiée.
     *
     * @param  \App\Models\Article  $article L'instance de l'article à modifier.
     * @return \Illuminate\View\View La vue du formulaire de modification.
     */
    public function edit(Article $article): \Illuminate\View\View
    {
        $categories = Categorie::all();
        $fournisseurs = Fournisseur::all();
        $emplacements = Emplacement::all();
        return view('articles.edit', compact('article', 'categories', 'fournisseurs', 'emplacements'));
    }

    /**
     * Met à jour la ressource spécifiée dans la base de données.
     *
     * @param  \App\Http\Requests\UpdateArticleRequest  $request La requête de mise à jour validée.
     * @param  \App\Models\Article  $article L'instance de l'article à mettre à jour.
     * @return \Illuminate\Http\RedirectResponse Une redirection vers la liste des articles avec un message de succès.
     */
    public function update(UpdateArticleRequest $request, Article $article): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image_path')) {
            // Delete old image if it exists
            if ($article->image_path) {
                Storage::disk('public')->delete($article->image_path);
            }
            $path = $request->file('image_path')->store('articles', 'public');
            $data['image_path'] = $path;
        }

        $article->update($data);

        return redirect()->route('articles.index')->with('success', 'Article mis à jour avec succès.');
    }

    /**
     * Supprime la ressource spécifiée de la base de données.
     *
     * @param  \App\Models\Article  $article L'instance de l'article à supprimer.
     * @return \Illuminate\Http\RedirectResponse Une redirection vers la liste des articles avec un message de succès.
     */
    public function destroy(Article $article): \Illuminate\Http\RedirectResponse
    {
        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Article supprimé avec succès.');
    }

    /**
     * Display a listing of the resource for public users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function productList(Request $request): \Illuminate\View\View
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $articles = Article::with('categorie')
            ->when($search, fn($query, $term) => $query->searchByText($term))
            ->when($category, fn($query, $catId) => $query->where('category_id', $catId))
            ->latest()
            ->paginate(10); // Paginate for better performance

        $categories = Categorie::all();

        return view('products.index', compact('articles', 'categories', 'search', 'category'));
    }

    /**
     * Display the specified resource for public users.
     *
     * @param  string $id
     * @return \Illuminate\View\View
     */
    public function productShow(string $id): \Illuminate\View\View
    {
        $article = Article::with('categorie', 'fournisseur', 'emplacement')->findOrFail($id);
        return view('products.show', compact('article'));
    }
}
