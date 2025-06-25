<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
// Si vous avez créé des Requests spécifiques, décommentez et utilisez-les
// use App\Http\Requests\StoreArticleRequest;
// use App\Http\Requests\UpdateArticleRequest;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Logique existante pour la vue d'index admin/backend des articles
        // Pour la page d'accueil, nous allons créer une méthode dédiée ou utiliser un autre contrôleur.
        // Cependant, la demande spécifie "ArticleController et une route GET / pour charger la vue d’accueil"
        // Donc, nous allons adapter cette méthode ou créer une nouvelle méthode pour la page d'accueil.
        // Pour l'instant, gardons la logique admin ici.
        $articles = Article::latest()->paginate(10); // Exemple pour une liste d'articles admin
        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Logique pour afficher le formulaire de création d'article (admin)
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreArticleRequest $request) // Utilisez StoreArticleRequest si défini
    public function store(Request $request)
    {
        // Logique pour enregistrer un nouvel article (admin)
        // $validatedData = $request->validated(); // Si vous utilisez StoreArticleRequest
        // Article::create($validatedData);
        // return redirect()->route('articles.index')->with('success', 'Article créé avec succès.');
        return redirect()->route('articles.index'); // Placeholder
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        // Logique pour afficher un article spécifique (peut être utilisé pour le front aussi)
        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        // Logique pour afficher le formulaire d'édition d'article (admin)
        return view('articles.edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateArticleRequest $request, Article $article) // Utilisez UpdateArticleRequest si défini
    public function update(Request $request, Article $article)
    {
        // Logique pour mettre à jour un article (admin)
        // $validatedData = $request->validated(); // Si vous utilisez UpdateArticleRequest
        // $article->update($validatedData);
        // return redirect()->route('articles.index')->with('success', 'Article mis à jour avec succès.');
        return redirect()->route('articles.index'); // Placeholder
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        // Logique pour supprimer un article (admin)
        // $article->delete();
        // return redirect()->route('articles.index')->with('success', 'Article supprimé avec succès.');
        return redirect()->route('articles.index'); // Placeholder
    }

    /**
     * Display the homepage with paginated articles.
     * Cette méthode sera utilisée pour la route GET /
     */
    public function welcome()
    {
        $articles = Article::with('category') // Eager load category
                           ->orderBy('created_at', 'desc') // Trier par les plus récents
                           ->paginate(12); // Paginer par 12 articles par page

        // Vous pouvez ajouter d'autres données ici si nécessaire pour la vue welcome
        // Par exemple, les catégories pour le menu, les promotions, etc.
        $categories = \App\Models\Category::all(); // Exemple

        return view('welcome', compact('articles', 'categories'));
    }
}
