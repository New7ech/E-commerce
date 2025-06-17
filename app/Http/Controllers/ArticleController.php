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

/**
 * Contrôleur pour la gestion des articles.
 * Gère les opérations CRUD pour les articles ainsi que l'affichage public des produits.
 */
class ArticleController extends Controller
{
    /**
     * Affiche une liste des articles pour l'administration, avec une fonctionnalité de recherche.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP, peut contenir un terme de recherche.
     * @return \Illuminate\View\View La vue contenant la liste des articles pour l'administration.
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $search = $request->input('search'); // Récupère le terme de recherche s'il existe

        // Construit la requête pour les articles, applique la recherche si un terme est fourni,
        // et trie les résultats par date de création (les plus récents d'abord).
        $articles = Article::when($search, fn($query, $term) => $query->searchByText($term))
                            ->latest()
                            ->get();

        return view('articles.index', compact('articles'));
    }

    /**
     * Affiche le formulaire de création d'un nouvel article.
     * Prépare les données nécessaires (catégories, fournisseurs, emplacements, utilisateurs) pour le formulaire.
     *
     * @return \Illuminate\View\View La vue du formulaire de création d'article.
     */
    public function create(): \Illuminate\View\View
    {
        $categories = Categorie::all(); // Récupère toutes les catégories
        $fournisseurs = Fournisseur::all(); // Récupère tous les fournisseurs
        $emplacements = Emplacement::all(); // Récupère tous les emplacements
        $users = User::all(); // Récupère tous les utilisateurs (pour 'created_by' ou autre)
        return view('articles.create', compact('categories', 'fournisseurs', 'emplacements', 'users'));
    }

    /**
     * Enregistre un nouvel article dans la base de données.
     * Gère également le téléversement de l'image de l'article si elle est fournie.
     *
     * @param  \App\Http\Requests\StoreArticleRequest  $request La requête validée contenant les données de l'article.
     * @return \Illuminate\Http\RedirectResponse Une redirection vers la liste des articles avec un message de succès.
     */
    public function store(StoreArticleRequest $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated(); // Récupère les données validées

        // Si une image est téléversée, la stocke et met à jour le chemin dans les données
        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('articles', 'public'); // Stocke dans 'storage/app/public/articles'
            $data['image_path'] = $path;
        }

        // Crée l'article avec les données et l'ID de l'utilisateur authentifié
        $article = Article::create($data + ['created_by' => auth()->id()]);

        return redirect()->route('articles.index')->with('success', 'Article créé avec succès.');
    }

    /**
     * Affiche les détails d'un article spécifique (pour l'administration).
     *
     * @param  \App\Models\Article  $article L'instance de l'article à afficher.
     * @return \Illuminate\View\View La vue affichant les détails de l'article.
     */
    public function show(Article $article): \Illuminate\View\View
    {
        return view('articles.show', compact('article'));
    }

    /**
     * Affiche le formulaire de modification d'un article existant.
     * Prépare les données nécessaires (catégories, fournisseurs, emplacements) pour le formulaire.
     *
     * @param  \App\Models\Article  $article L'instance de l'article à modifier.
     * @return \Illuminate\View\View La vue du formulaire de modification d'article.
     */
    public function edit(Article $article): \Illuminate\View\View
    {
        $categories = Categorie::all(); // Récupère toutes les catégories
        $fournisseurs = Fournisseur::all(); // Récupère tous les fournisseurs
        $emplacements = Emplacement::all(); // Récupère tous les emplacements
        return view('articles.edit', compact('article', 'categories', 'fournisseurs', 'emplacements'));
    }

    /**
     * Met à jour un article spécifique dans la base de données.
     * Gère également le remplacement de l'image de l'article si une nouvelle est fournie.
     *
     * @param  \App\Http\Requests\UpdateArticleRequest  $request La requête validée contenant les données de mise à jour.
     * @param  \App\Models\Article  $article L'instance de l'article à mettre à jour.
     * @return \Illuminate\Http\RedirectResponse Une redirection vers la liste des articles avec un message de succès.
     */
    public function update(UpdateArticleRequest $request, Article $article): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated(); // Récupère les données validées

        // Si une nouvelle image est téléversée
        if ($request->hasFile('image_path')) {
            // Supprime l'ancienne image si elle existe
            if ($article->image_path) {
                Storage::disk('public')->delete($article->image_path);
            }
            // Stocke la nouvelle image et met à jour le chemin
            $path = $request->file('image_path')->store('articles', 'public');
            $data['image_path'] = $path;
        }

        $article->update($data); // Met à jour l'article

        return redirect()->route('articles.index')->with('success', 'Article mis à jour avec succès.');
    }

    /**
     * Supprime un article spécifique de la base de données.
     *
     * @param  \App\Models\Article  $article L'instance de l'article à supprimer.
     * @return \Illuminate\Http\RedirectResponse Une redirection vers la liste des articles avec un message de succès.
     */
    public function destroy(Article $article): \Illuminate\Http\RedirectResponse
    {
        // Note: La suppression de l'image associée pourrait être ajoutée ici si nécessaire
        // if ($article->image_path) {
        //     Storage::disk('public')->delete($article->image_path);
        // }
        $article->delete(); // Supprime l'article

        return redirect()->route('articles.index')->with('success', 'Article supprimé avec succès.');
    }

    /**
     * Affiche une liste paginée des produits pour les utilisateurs publics.
     * Permet le filtrage par recherche, catégorie, prix et le tri.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant les paramètres de filtrage et de tri.
     * @return \Illuminate\View\View La vue de la liste des produits.
     */
    public function productList(Request $request): \Illuminate\View\View
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $price_min = $request->input('price_min');
        $price_max = $request->input('price_max');
        $sort_by = $request->input('sort_by');

        $articlesQuery = Article::with('categorie') // Charge la relation catégorie pour chaque article (optimisation)
            // Applique le filtre de recherche textuelle si fourni
            ->when($search, fn($query, $term) => $query->searchByText($term))
            // Applique le filtre par catégorie si fourni
            ->when($category, fn($query, $catId) => $query->where('category_id', $catId));

        // Applique les filtres de fourchette de prix
        if ($price_min && $price_max) {
            $articlesQuery->whereBetween('prix', [(float)$price_min, (float)$price_max]);
        } elseif ($price_min) {
            $articlesQuery->where('prix', '>=', (float)$price_min);
        } elseif ($price_max) {
            $articlesQuery->where('prix', '<=', (float)$price_max);
        }

        // Applique le tri selon le critère choisi
        switch ($sort_by) {
            case 'price_asc':
                $articlesQuery->orderBy('prix', 'asc');
                break;
            case 'price_desc':
                $articlesQuery->orderBy('prix', 'desc');
                break;
            case 'name_asc':
                $articlesQuery->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $articlesQuery->orderBy('name', 'desc');
                break;
            case 'created_at_desc':
                $articlesQuery->orderBy('created_at', 'desc');
                break;
            default:
                $articlesQuery->latest(); // Tri par défaut : les plus récents d'abord
        }

        $articles = $articlesQuery->paginate(10); // Pagine les résultats

        $categories = Categorie::all(); // Pour le menu déroulant de filtre par catégorie

        // Pour les boutons de liste de souhaits sur la liste des produits :
        // récupère les IDs des articles dans la liste de souhaits de l'utilisateur actuel
        $userWishlistArticleIds = [];
        if (auth()->check()) {
            $userWishlistArticleIds = auth()->user()->wishlistedArticles()->pluck('articles.id')->toArray();
        }

        return view('products.index', compact(
            'articles',
            'categories',
            'search',
            'category',
            'price_min',
            'price_max',
            'sort_by',
            'userWishlistArticleIds' // Transmet les IDs de la liste de souhaits à la vue
        ));
    }

    /**
     * Affiche la page de détail d'un produit spécifique pour les utilisateurs publics.
     * Charge également les articles similaires et vérifie si l'article est dans la liste de souhaits de l'utilisateur.
     *
     * @param  string $id L'ID de l'article à afficher.
     * @return \Illuminate\View\View La vue de la page du produit.
     */
    public function productShow(string $id): \Illuminate\View\View
    {
        // Charge l'article avec ses relations (catégorie, fournisseur, emplacement) pour éviter les requêtes N+1.
        $article = Article::with('categorie', 'fournisseur', 'emplacement')->findOrFail($id);

        $relatedArticles = collect(); // Initialise une collection vide pour les articles similaires

        // Récupère des articles similaires de la même catégorie, si la catégorie existe
        if ($article->categorie) { // Vérifie si l'article a une catégorie associée
            $relatedArticles = Article::with('categorie') // Charge la catégorie pour les articles similaires
                ->where('category_id', $article->category_id) // Doit être dans la même catégorie
                ->where('id', '!=', $article->id) // Exclut l'article actuel
                ->inRandomOrder() // Affiche des articles similaires aléatoires
                ->limit(4) // Limite le nombre d'articles similaires affichés
                ->get();
        }

        // Vérifie si l'article actuel est dans la liste de souhaits de l'utilisateur authentifié
        $isInWishlist = false;
        if (auth()->check()) {
            // Cette vérification est efficace si la relation wishlistedArticles est utilisée.
            // Elle effectue une requête ciblée si la relation n'est pas déjà chargée.
            $isInWishlist = auth()->user()->wishlistedArticles()->where('articles.id', $article->id)->exists();
        }

        return view('products.show', compact('article', 'relatedArticles', 'isInWishlist'));
    }
}
