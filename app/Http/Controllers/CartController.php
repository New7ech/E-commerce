<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Contrôleur pour la gestion du panier d'achats.
 * Utilise la session pour stocker les articles du panier.
 */
class CartController extends Controller
{
    /**
     * Constructeur du contrôleur.
     * S'assure que la session PHP est démarrée, bien que Laravel gère généralement cela automatiquement
     * via le middleware StartSession. Ce constructeur explicite peut être redondant.
     */
    public function __construct()
    {
        // S'assurer que la session est démarrée.
        // Note : Laravel démarre généralement la session via son middleware.
        // Cette vérification manuelle est souvent inutile dans un contexte Laravel standard.
        if (session_status() == PHP_SESSION_NONE && !app()->runningInConsole() && !app()->runningUnitTests()) {
             session_start();
        }
    }

    /**
     * Affiche les articles présents dans le panier.
     * Récupère les articles de la session, calcule le total et affiche la vue du panier.
     *
     * @return \Illuminate\View\View La vue du panier avec les articles et le prix total.
     */
    public function index(): \Illuminate\View\View
    {
        $cart = Session::get('cart', []); // Récupère le panier de la session, ou un tableau vide par défaut.
        $articlesInCart = [];
        $totalPrice = 0;

        if (!empty($cart)) {
            $articleIds = array_keys($cart); // Obtient les IDs des articles dans le panier.
            // Récupère les informations des articles depuis la base de données.
            $articles = Article::whereIn('id', $articleIds)->get()->keyBy('id');

            foreach ($cart as $id => $item) {
                if (isset($articles[$id])) {
                    $article = $articles[$id];
                    $quantity = $item['quantity'];
                    $subtotal = $article->prix * $quantity; // Calcule le sous-total pour cet article.
                    $articlesInCart[] = [
                        'id' => $article->id,
                        'name' => $article->name,
                        'prix' => $article->prix,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                        // Ajouter 'image_path' si disponible dans le modèle Article et nécessaire pour la vue.
                        // 'image_path' => $article->image_path,
                    ];
                    $totalPrice += $subtotal; // Ajoute au prix total du panier.
                }
            }
        }

        return view('cart.index', compact('articlesInCart', 'totalPrice'));
    }

    /**
     * Ajoute un article au panier ou augmente sa quantité si déjà présent.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP, contenant potentiellement la quantité.
     * @param  \App\Models\Article  $article L'article à ajouter au panier.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page du panier avec un message de succès.
     */
    public function add(Request $request, Article $article) // Return type hint removed for dual response type
    {
        $quantity = $request->input('quantity', 1); // Quantité par défaut à 1 si non fournie.
        $cart = Session::get('cart', []); // Récupère le panier actuel.

        // Vérifier le stock de l'article avant d'ajouter
        if ($article->stock < $quantity) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Stock insuffisant pour cet article.'], 400);
            }
            return redirect()->back()->with('error', 'Stock insuffisant pour cet article.');
        }

        $currentQuantityInCart = 0;
        if (isset($cart[$article->id])) {
            $currentQuantityInCart = $cart[$article->id]['quantity'];
        }

        if ($article->stock < ($currentQuantityInCart + $quantity)) {
             if ($request->expectsJson()) {
                return response()->json(['error' => 'Quantité demandée dépasse le stock disponible, en tenant compte de votre panier actuel.'], 400);
            }
            return redirect()->back()->with('error', 'Quantité demandée dépasse le stock disponible, en tenant compte de votre panier actuel.');
        }


        // Si l'article est déjà dans le panier, augmente la quantité.
        if (isset($cart[$article->id])) {
            $cart[$article->id]['quantity'] += $quantity;
        } else {
            // Sinon, ajoute le nouvel article avec la quantité spécifiée.
            $cart[$article->id] = [
                'quantity' => $quantity,
                // Vous pouvez stocker plus d'infos si besoin, mais attention à la taille de la session
                // 'name' => $article->title,
                // 'price' => $article->promo_price ?? $article->price,
                // 'image' => $article->image_url
            ];
        }

        Session::put('cart', $cart); // Sauvegarde le panier mis à jour dans la session.

        // Mettre à jour le stock de l'article (si vous gérez le stock de cette manière)
        // $article->decrement('stock', $quantity); // Attention: à faire seulement à la validation de commande normalement

        $totalItemsInCart = 0;
        foreach ($cart as $item) {
            $totalItemsInCart += $item['quantity'];
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => 'Article ajouté au panier avec succès !',
                'cartTotalItems' => $totalItemsInCart // Nombre total d'articles différents ou quantité totale?
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Article ajouté au panier avec succès !');
    }

    /**
     * Met à jour la quantité d'un article dans le panier.
     * Si la quantité est mise à 0 ou moins, l'article est retiré du panier.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant la nouvelle quantité.
     * @param  \App\Models\Article  $article L'article dont la quantité doit être mise à jour.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page du panier avec un message de succès ou d'erreur.
     */
    public function update(Request $request, Article $article): \Illuminate\Http\RedirectResponse
    {
        $quantity = $request->input('quantity'); // Récupère la nouvelle quantité.
        $cart = Session::get('cart', []);

        // Si l'article est dans le panier et la quantité est positive.
        if (isset($cart[$article->id]) && $quantity > 0) {
            $cart[$article->id]['quantity'] = $quantity; // Met à jour la quantité.
            Session::put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Panier mis à jour avec succès !');
        } elseif (isset($cart[$article->id]) && $quantity <= 0) {
            // Si la quantité est de 0 ou moins, retire l'article du panier.
            unset($cart[$article->id]);
            Session::put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Article retiré du panier.');
        }

        return redirect()->route('cart.index')->with('error', 'Quantité invalide ou article non trouvé dans le panier.');
    }

    /**
     * Retire un article spécifique du panier.
     *
     * @param  \App\Models\Article  $article L'article à retirer.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page du panier avec un message de succès ou d'erreur.
     */
    public function remove(Article $article): \Illuminate\Http\RedirectResponse
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$article->id])) {
            unset($cart[$article->id]); // Retire l'article du tableau du panier.
            Session::put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Article retiré du panier avec succès !');
        }

        return redirect()->route('cart.index')->with('error', 'Article non trouvé dans le panier.');
    }

    /**
     * Vide entièrement le panier.
     *
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page du panier avec un message de succès.
     */
    public function clear(): \Illuminate\Http\RedirectResponse
    {
        Session::forget('cart'); // Supprime toutes les données du panier de la session.
        return redirect()->route('cart.index')->with('success', 'Panier vidé avec succès !');
    }
}
