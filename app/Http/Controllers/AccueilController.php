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

    // Les autres méthodes (create, store, show, edit, update, destroy)
    // peuvent être supprimées si elles ne sont pas utilisées pour la nouvelle page d'accueil.
    // Pour l'instant, nous les laissons commentées ou vides si elles étaient déjà ainsi.

    /**
     * Affiche le formulaire de création d'une nouvelle ressource.
     * (Méthode actuellement non utilisée pour la page d'accueil)
     */
    // public function create() { }

    /**
     * Enregistre une nouvelle ressource dans la base de données.
     * (Méthode actuellement non utilisée pour la page d'accueil)
     */
    // public function store(Request $request) { }

    /**
     * Affiche une ressource spécifique.
     * (Méthode actuellement non utilisée pour la page d'accueil)
     */
    // public function show($id) { }

    /**
     * Affiche le formulaire de modification d'une ressource spécifique.
     * (Méthode actuellement non utilisée pour la page d'accueil)
     */
    // public function edit($id) { }

    /**
     * Met à jour une ressource spécifique dans la base de données.
     * (Méthode actuellement non utilisée pour la page d'accueil)
     */
    // public function update(Request $request, $id) { }

    /**
     * Supprime une ressource spécifique de la base de données.
     * (Méthode actuellement non utilisée pour la page d'accueil)
     */
    // public function destroy($id) { }
}
