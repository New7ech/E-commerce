<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function __construct()
    {
        // Ensure session is started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Display the cart items.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cart = Session::get('cart', []);
        $articlesInCart = [];
        $totalPrice = 0;

        if (!empty($cart)) {
            $articleIds = array_keys($cart);
            $articles = Article::whereIn('id', $articleIds)->get()->keyBy('id');

            foreach ($cart as $id => $item) {
                if (isset($articles[$id])) {
                    $article = $articles[$id];
                    $quantity = $item['quantity'];
                    $subtotal = $article->prix * $quantity;
                    $articlesInCart[] = [
                        'id' => $article->id,
                        'name' => $article->name,
                        'prix' => $article->prix,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                        // Add image_url if you have it in your Article model
                        // 'image_url' => $article->image_url,
                    ];
                    $totalPrice += $subtotal;
                }
            }
        }

        return view('cart.index', compact('articlesInCart', 'totalPrice'));
    }

    /**
     * Add an article to the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request, Article $article)
    {
        $quantity = $request->input('quantity', 1);
        $cart = Session::get('cart', []);

        if (isset($cart[$article->id])) {
            $cart[$article->id]['quantity'] += $quantity;
        } else {
            $cart[$article->id] = [
                'quantity' => $quantity,
            ];
        }

        Session::put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Article added to cart successfully!');
    }

    /**
     * Update the quantity of an article in the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Article $article)
    {
        $quantity = $request->input('quantity');
        $cart = Session::get('cart', []);

        if (isset($cart[$article->id]) && $quantity > 0) {
            $cart[$article->id]['quantity'] = $quantity;
            Session::put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Cart updated successfully!');
        } elseif (isset($cart[$article->id]) && $quantity <= 0) {
            // If quantity is 0 or less, remove the item
            unset($cart[$article->id]);
            Session::put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Article removed from cart.');
        }

        return redirect()->route('cart.index')->with('error', 'Invalid quantity or item not in cart.');
    }

    /**
     * Remove an article from the cart.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Article $article)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$article->id])) {
            unset($cart[$article->id]);
            Session::put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Article removed from cart successfully!');
        }

        return redirect()->route('cart.index')->with('error', 'Article not found in cart.');
    }

    /**
     * Clear all items from the cart.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        Session::forget('cart');
        return redirect()->route('cart.index')->with('success', 'Cart cleared successfully!');
    }
}
