<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // Pour les transactions de base de données

/**
 * Contrôleur pour gérer le processus de paiement (checkout).
 */
class CheckoutController extends Controller
{
    /**
     * Affiche la page de paiement avec les articles du panier.
     * Vérifie la disponibilité et les quantités des articles avant d'afficher la page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse La vue de paiement ou une redirection si le panier est vide/invalide.
     */
    public function index(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $cart = Session::get('cart', []); // Récupère le panier depuis la session.
        if (empty($cart)) {
            // Redirige vers la liste des produits si le panier est vide.
            return redirect()->route('products.index')->with('info', 'Votre panier est vide. Veuillez ajouter des articles avant de passer à la caisse.');
        }

        $articlesInCart = [];
        $totalPrice = 0;
        $articleIds = array_keys($cart);
        // Récupère les articles de la base de données pour vérifier les informations.
        $articles = Article::whereIn('id', $articleIds)->get()->keyBy('id');

        foreach ($cart as $id => $item) {
            if (isset($articles[$id])) {
                $article = $articles[$id];
                $quantity = $item['quantity'];
                // S'assure que la quantité ne dépasse pas le stock disponible avant le paiement.
                if ($quantity > $article->quantite) {
                    // Optionnellement, ajuster la quantité dans le panier ici et informer l'utilisateur.
                    // Pour l'instant, redirige vers le panier avec une erreur.
                    return redirect()->route('cart.index')->with('error', "La quantité pour l'article '{$article->name}' dépasse le stock disponible. Veuillez mettre à jour votre panier.");
                }
                $subtotal = $article->prix * $quantity;
                $articlesInCart[] = [
                    'id' => $article->id,
                    'name' => $article->name,
                    'prix' => $article->prix,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ];
                $totalPrice += $subtotal;
            }
        }

        // Si des articles ont été retirés parce qu'ils ne sont plus disponibles ou que les IDs ne correspondent pas.
        if (count($articlesInCart) !== count($cart)) {
             // Recalcule le panier si certains articles étaient invalides / retirés.
            $validCart = [];
            foreach($articlesInCart as $validItem) {
                $validCart[$validItem['id']] = ['quantity' => $validItem['quantity']];
            }
            Session::put('cart', $validCart); // Met à jour le panier en session avec seulement les articles valides.
            // Si tous les articles sont devenus invalides.
            if(empty($articlesInCart)){
                 return redirect()->route('products.index')->with('info', 'Certains articles de votre panier ne sont plus disponibles. Votre panier a été mis à jour.');
            }
             // Informer l'utilisateur que son panier a été mis à jour.
             return redirect()->route('cart.index')->with('info', 'Certains articles de votre panier ont été mis à jour. Veuillez vérifier avant de passer à la caisse.');
        }

        // Affiche la vue de paiement avec les articles et le prix total.
        return view('checkout.index', compact('articlesInCart', 'totalPrice'));
    }

    /**
     * Traite le paiement : valide les entrées, crée la commande, met à jour le stock et simule le paiement.
     * Gère à la fois les utilisateurs authentifiés et les invités.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant les informations de paiement.
     * @return \Illuminate\Http\RedirectResponse Redirige vers une page de succès ou d'erreur.
     */
    public function process(Request $request): \Illuminate\Http\RedirectResponse
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index')->with('info', 'Votre panier est vide.');
        }

        // Détermine si l'utilisateur actuel est un invité.
        $isGuest = !auth()->check();

        // Règles de validation de base pour les informations de livraison et de facturation.
        $validationRules = [
            'shipping_name' => 'required|string|max:255', // Nom pour la livraison
            'shipping_address' => 'required|string|max:255', // Adresse de livraison
            'shipping_city' => 'required|string|max:255', // Ville de livraison
            'shipping_postal_code' => 'required|string|max:20', // Code postal de livraison
            'shipping_country' => 'required|string|max:255', // Pays de livraison
            'billing_same_as_shipping' => 'nullable|boolean', // Case à cocher pour adresse de facturation identique
            'billing_name' => 'required_if:billing_same_as_shipping,false|nullable|string|max:255', // Nom pour la facturation
            'billing_address' => 'required_if:billing_same_as_shipping,false|nullable|string|max:255', // Adresse de facturation
            'billing_city' => 'required_if:billing_same_as_shipping,false|nullable|string|max:255', // Ville de facturation
            'billing_postal_code' => 'required_if:billing_same_as_shipping,false|nullable|string|max:20', // Code postal de facturation
            'billing_country' => 'required_if:billing_same_as_shipping,false|nullable|string|max:255', // Pays de facturation
            'payment_method' => 'required|string', // Méthode de paiement (ex: 'stripe', 'paypal')
        ];

        // Ajoute la règle de validation pour l'e-mail des utilisateurs invités.
        if ($isGuest) {
            // 'guest_email' est utilisé dans le formulaire pour les invités afin d'éviter les conflits
            // avec un éventuel champ 'email' des données de l'utilisateur connecté.
            $validationRules['guest_email'] = 'required|email|max:255';
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return redirect()->route('checkout.index')
                        ->withErrors($validator)
                        ->withInput(); // Redirige avec les erreurs et les anciennes entrées.
        }

        // --- Simulation du Traitement du Paiement ---
        $paymentSuccessful = true; // Simule un succès de paiement. Mettre à false pour tester l'échec.

        if (!$paymentSuccessful) {
            return redirect()->route('checkout.index')->with('error', 'Le paiement a échoué. Veuillez réessayer.');
        }

        // --- Création de la Commande : Envelopper dans une transaction de base de données pour l'atomicité ---
        DB::beginTransaction();
        try {
            $totalPrice = 0;
            $articleDetailsForOrder = []; // Pour stocker les détails nécessaires à la création des OrderItems.
            $articleIds = array_keys($cart);

            // Récupère les articles de la BDD et les verrouille pour mise à jour afin d'éviter les conditions de concurrence sur le stock.
            $articlesFromDb = Article::whereIn('id', $articleIds)->lockForUpdate()->get()->keyBy('id');

            // Valide le stock et calcule le prix total.
            foreach ($cart as $id => $item) {
                if (!isset($articlesFromDb[$id])) {
                    DB::rollBack(); // L'article a été retiré de la BDD après l'initialisation du panier.
                    return redirect()->route('cart.index')->with('error', "L'article avec ID {$id} n'est plus disponible.");
                }
                $article = $articlesFromDb[$id];
                $requestedQuantity = $item['quantity'];

                if ($article->quantite < $requestedQuantity) {
                    DB::rollBack();
                    return redirect()->route('cart.index')->with('error', "Stock insuffisant pour l'article '{$article->name}'. Demandé : {$requestedQuantity}, Disponible : {$article->quantite}. Veuillez mettre à jour votre panier.");
                }
                $subtotal = $article->prix * $requestedQuantity;
                $totalPrice += $subtotal;
                $articleDetailsForOrder[$id] = [ // Stocke les détails pour créer OrderItem plus tard.
                    'article' => $article, // Conserve le modèle pour la mise à jour du stock.
                    'quantity' => $requestedQuantity,
                    'price' => $article->prix, // Stocke le prix au moment de l'achat.
                ];
            }

            // Prépare les détails de livraison à partir de la requête.
            $shippingDetails = [
                'name' => $request->shipping_name,
                'address' => $request->shipping_address,
                'city' => $request->shipping_city,
                'postal_code' => $request->shipping_postal_code,
                'country' => $request->shipping_country,
            ];

            $billingDetails = $shippingDetails; // Par défaut, identique à la livraison.
            if (!$request->boolean('billing_same_as_shipping')) {
                $billingDetails = [
                    'name' => $request->billing_name,
                    'address' => $request->billing_address,
                    'city' => $request->billing_city,
                    'postal_code' => $request->billing_postal_code,
                    'country' => $request->billing_country,
                ];
            }

            // Crée l'enregistrement de la commande.
            $order = Order::create([
                'user_id' => auth()->check() ? auth()->id() : null, // Null pour les invités.
                'email' => $isGuest ? $request->guest_email : auth()->user()->email, // E-mail de l'invité ou de l'utilisateur connecté.
                'shipping_name' => $shippingDetails['name'],
                'shipping_address' => $shippingDetails['address'],
                'shipping_city' => $shippingDetails['city'],
                'shipping_postal_code' => $shippingDetails['postal_code'],
                'shipping_country' => $shippingDetails['country'],
                'billing_name' => $billingDetails['name'],
                'billing_address' => $billingDetails['address'],
                'billing_city' => $billingDetails['city'],
                'billing_postal_code' => $billingDetails['postal_code'],
                'billing_country' => $billingDetails['country'],
                'total_amount' => $totalPrice,
                'status' => 'pending_payment', // Ou 'processing' si le paiement est confirmé.
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending', // Statut de paiement par défaut.
            ]);

            // Crée les articles de la commande et déduit le stock.
            foreach ($articleDetailsForOrder as $id => $details) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'article_id' => $id,
                    'quantity' => $details['quantity'],
                    'price' => $details['price'], // Prix au moment de la commande.
                ]);

                // Diminue la quantité en stock pour l'article.
                $articleToUpdate = $details['article']; // Conserve l'instance du modèle Article verrouillée.
                $articleToUpdate->quantite -= $details['quantity'];
                $articleToUpdate->save();
            }

            // Si la simulation de paiement a réussi, met à jour le statut de la commande.
            if ($paymentSuccessful) {
                $order->status = 'processing'; // Ou 'completed' si aucun traitement supplémentaire post-paiement.
                $order->payment_status = 'paid'; // Met à jour le statut du paiement.
                $order->save();
            }

            DB::commit(); // Tout s'est bien passé, valide la transaction.

            Session::forget('cart'); // Vide le panier après la passation de commande réussie.

            // Redirige vers une page de succès (ex: accueil ou page de confirmation de commande dédiée).
            return redirect()->route('home')->with('success', 'Commande passée avec succès ! ID de commande : ' . $order->id);

        } catch (\Exception $e) {
            DB::rollBack(); // Quelque chose s'est mal passé, annule la transaction.
            // Log::error('Le traitement de la commande a échoué : ' . $e->getMessage()); // Bonne pratique : enregistrer l'erreur réelle.
            // Redirige vers la page de paiement avec un message d'erreur et les entrées.
            return redirect()->route('checkout.index')->with('error', 'Le traitement de la commande a échoué. Veuillez réessayer. Détails : ' . $e->getMessage())->withInput();
        }
    }
}
