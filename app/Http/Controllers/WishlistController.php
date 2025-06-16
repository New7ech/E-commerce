<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display the authenticated user's wishlist.
     * Eager loads associated articles and their categories.
     * Paginate results for better performance with large wishlists.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        // Fetch wishlist items for the current user, eager load related articles and their categories.
        // Paginate the results for better performance if the wishlist is long.
        $wishlistItems = $user->wishlists() // Using the relationship from User model
                              ->with('article.categorie') // Eager load article and its category
                              ->latest('wishlists.created_at') // Order by when item was added to wishlist
                              ->paginate(10); // Paginate results

        return view('wishlist.index', compact('wishlistItems'));
    }

    /**
     * Add an article to the authenticated user's wishlist.
     * Prevents duplicates.
     *
     * @param  \App\Models\Article  $article The article to add.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Article $article)
    {
        $user = Auth::user();

        // Attempt to create the wishlist item. If it already exists, it won't be duplicated due to composite primary key.
        // firstOrCreate will retrieve the first record matching the attributes, or create it if it doesn't exist.
        $wishlistItem = Wishlist::firstOrCreate(
            [
                'user_id' => $user->id,
                'article_id' => $article->id,
            ]
            // No additional attributes needed for creation beyond the ones being checked.
        );

        if ($wishlistItem->wasRecentlyCreated) {
            return redirect()->back()->with('success', 'Article ajouté à votre liste de souhaits !');
        } else {
            return redirect()->back()->with('info', 'Cet article est déjà dans votre liste de souhaits.');
        }
    }

    /**
     * Remove an article from the authenticated user's wishlist.
     *
     * @param  \App\Models\Article  $article The article to remove.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Article $article)
    {
        $user = Auth::user();

        // Attempt to delete the wishlist item.
        // This is more direct than fetching first then deleting.
        $deleted = $user->wishlists()->where('article_id', $article->id)->delete();

        if ($deleted) {
            return redirect()->back()->with('success', 'Article retiré de votre liste de souhaits.');
        }

        return redirect()->back()->with('error', 'Cet article n\'est pas dans votre liste de souhaits ou une erreur est survenue.');
    }
}
