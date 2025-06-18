<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôleur pour la gestion de la liste de souhaits des utilisateurs.
 * Permet aux utilisateurs authentifiés d'ajouter, de retirer et de visualiser des articles dans leur liste de souhaits.
 */
class WishlistController extends Controller
{
    /**
     * Affiche la liste de souhaits de l'utilisateur authentifié.
     * Charge les articles associés et leurs catégories pour un affichage optimisé.
     * Pagine les résultats pour une meilleure performance avec de longues listes de souhaits.
     *
     * @return \Illuminate\View\View La vue affichant la liste de souhaits.
     */
    public function index(): \Illuminate\View\View
    {
        $user = Auth::user(); // Récupère l'utilisateur authentifié.
        // Récupère les éléments de la liste de souhaits pour l'utilisateur actuel,
        // charge en avance les articles liés et leurs catégories (Eager Loading).
        // Pagine les résultats si la liste de souhaits est longue.
        $wishlistItems = $user->wishlists() // Utilise la relation définie dans le modèle User.
                              ->with('article.categorie') // Charge l'article et sa catégorie.
                              ->latest('wishlists.created_at') // Trie par date d'ajout à la liste de souhaits (plus récent d'abord).
                              ->paginate(10); // Pagine les résultats (10 par page).

        return view('wishlist.index', compact('wishlistItems'));
    }

    /**
     * Ajoute un article à la liste de souhaits de l'utilisateur authentifié.
     * Empêche l'ajout de doublons si l'article est déjà présent.
     *
     * @param  \App\Models\Article  $article L'article à ajouter.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page précédente avec un message de succès ou d'information.
     */
    public function add(Article $article): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user(); // Récupère l'utilisateur authentifié.

        // Tente de créer l'élément dans la liste de souhaits.
        // `firstOrCreate` récupère le premier enregistrement correspondant aux attributs,
        // ou le crée s'il n'existe pas. Cela évite les doublons grâce à la clé primaire composite (user_id, article_id) sur la table wishlists.
        $wishlistItem = Wishlist::firstOrCreate(
            [
                'user_id' => $user->id,
                'article_id' => $article->id,
            ]
            // Aucun attribut supplémentaire n'est nécessaire pour la création au-delà de ceux vérifiés.
        );

        // Vérifie si l'élément a été récemment créé ou s'il existait déjà.
        if ($wishlistItem->wasRecentlyCreated) {
            return redirect()->back()->with('success', 'Article ajouté à votre liste de souhaits !');
        } else {
            return redirect()->back()->with('info', 'Cet article est déjà dans votre liste de souhaits.');
        }
    }

    /**
     * Retire un article de la liste de souhaits de l'utilisateur authentifié.
     *
     * @param  \App\Models\Article  $article L'article à retirer.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page précédente avec un message de succès ou d'erreur.
     */
    public function remove(Article $article): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user(); // Récupère l'utilisateur authentifié.

        // Tente de supprimer l'élément de la liste de souhaits.
        // Cette approche est plus directe que de récupérer d'abord l'élément puis de le supprimer.
        $deleted = $user->wishlists()->where('article_id', $article->id)->delete();

        if ($deleted) {
            return redirect()->back()->with('success', 'Article retiré de votre liste de souhaits.');
        }

        // Si l'élément n'a pas été trouvé (et donc pas supprimé) ou si une autre erreur survient.
        return redirect()->back()->with('error', 'Cet article n\'est pas dans votre liste de souhaits ou une erreur est survenue.');
    }
}
