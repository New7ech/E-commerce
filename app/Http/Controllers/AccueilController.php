<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Models\Categorie; // Ajout du modèle Categorie

/**
 * Contrôleur pour gérer la page d'accueil.
 */
class AccueilController extends Controller
{
    /**
     * Affiche la page d'accueil avec la liste des articles et des catégories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer tous les articles
        $articles = Article::latest()->paginate(12); // Paginer pour de meilleures performances
        $categories = Categorie::all(); // Récupérer toutes les catégories pour le menu

        // Passer les articles et les catégories à la vue 'welcome'
        return view('welcome', compact('articles', 'categories'));
    }

    // Les méthodes CRUD standard (create, store, show, edit, update, destroy)
    // ne sont pas utilisées ici car ArticleController@welcome gère la page d'accueil
    // et l'administration des articles se fait via ArticleController (pour l'admin).
    // Ce contrôleur pourrait être réutilisé pour une autre section "accueil" ou supprimé
    // s'il n'a plus d'utilité spécifique maintenant que ArticleController.welcome est la page principale.
    // Pour l'instant, nous allons seulement supprimer les méthodes commentées.
}
