<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Affiche une liste des articles pour la gestion administrative.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupère les articles paginés pour l'interface d'administration
        $articles = Article::with('category')->latest()->paginate(10);
        return view('articles.index', compact('articles'));
    }

    /**
     * Affiche le formulaire de création d'un nouvel article (admin).
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Categorie::all();
        $fournisseurs = \App\Models\Fournisseur::all(); // Assurez-vous que le modèle Fournisseur existe
        $emplacements = \App\Models\Emplacement::all(); // Assurez-vous que le modèle Emplacement existe
        return view('articles.create', compact('categories', 'fournisseurs', 'emplacements'));
    }

    /**
     * Enregistre un nouvel article dans la base de données (admin).
     *
     * @param  \App\Http\Requests\StoreArticleRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreArticleRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('articles_images', 'public');
            $validatedData['image_path'] = $path;
        }

        // Assurez-vous que les noms de champs correspondent à la base de données et au modèle Article.
        // Les FormRequests utilisent 'name', 'prix', 'quantite'.
        // Si le modèle utilise 'title', 'price', 'stock', il faudra mapper ici ou changer les FormRequests/modèle.
        // Pour l'instant, on suppose une correspondance directe ou que le modèle gère l'aliasing via $fillable.
        Article::create($validatedData);

        return redirect()->route('admin.articles.index')->with('success', 'Article créé avec succès.');
    }

    /**
     * Affiche un article spécifique (utilisé publiquement).
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\View\View
     */
    public function show(Article $article)
    {
        // Charge la catégorie pour l'affichage
        $article->load('category');
        return view('articles.show', compact('article'));
    }

    /**
     * Affiche le formulaire de modification d'un article (admin).
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\View\View
     */
    public function edit(Article $article)
    {
        $categories = Categorie::all();
        $fournisseurs = \App\Models\Fournisseur::all();
        $emplacements = \App\Models\Emplacement::all();
        return view('articles.edit', compact('article', 'categories', 'fournisseurs', 'emplacements'));
    }

    /**
     * Met à jour un article spécifique dans la base de données (admin).
     *
     * @param  \App\Http\Requests\UpdateArticleRequest  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('image_path')) {
            // Supprimer l'ancienne image si elle existe
            if ($article->image_path) {
                Storage::disk('public')->delete($article->image_path);
            }
            $path = $request->file('image_path')->store('articles_images', 'public');
            $validatedData['image_path'] = $path;
        }

        $article->update($validatedData);

        return redirect()->route('admin.articles.index')->with('success', 'Article mis à jour avec succès.');
    }

    /**
     * Supprime un article spécifique de la base de données (admin).
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Article $article)
    {
        // Supprimer l'image associée si elle existe
        if ($article->image_path) {
            Storage::disk('public')->delete($article->image_path);
        }
        $article->delete();
        return redirect()->route('admin.articles.index')->with('success', 'Article supprimé avec succès.');
    }

    /**
     * Affiche la page d'accueil avec les articles paginés.
     * Cette méthode sera utilisée pour la route GET /
     */
    public function welcome()
    {
        $articles = Article::with('category') // Eager load category
                           ->orderBy('created_at', 'desc') // Trier par les plus récents
                           ->paginate(12); // Paginer par 12 articles par page

        // Vous pouvez ajouter d'autres données ici si nécessaire pour la vue welcome
        // Par exemple, les catégories pour le menu, les promotions, etc.
        $categories = Categorie::all(); // Utiliser l'alias Categorie déjà importé

        return view('welcome', compact('articles', 'categories'));
    }

    /**
     * Affiche une liste publique des produits, paginée.
     * Peut être utilisé pour une page "Tous les produits".
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function productList(Request $request)
    {
        // Logique de filtrage et de tri potentielle ici basée sur $request
        // Exemple simple :
        $query = Article::with('category')->orderBy('created_at', 'desc');

        // Exemple de filtre par catégorie si un paramètre 'category' est présent dans la requête
        if ($request->has('category')) {
            $query->where('category_id', $request->input('category'));
        }

        // Exemple de recherche simple
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%") // Assumant que 'name' est le champ titre
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $articles = $query->paginate(15); // Paginer par 15 articles
        $categories = Categorie::all();

        // Cette vue pourrait être 'products.index' ou une autre vue dédiée
        // Pour l'instant, nous allons supposer qu'il existe une vue resources/views/products/index.blade.php
        return view('products.index', compact('articles', 'categories'));
    }
}
