<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // For database transactions

class CheckoutController extends Controller
{
    /**
     * Display the checkout page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index')->with('info', 'Your cart is empty. Please add items before proceeding to checkout.');
        }

        $articlesInCart = [];
        $totalPrice = 0;
        $articleIds = array_keys($cart);
        $articles = Article::whereIn('id', $articleIds)->get()->keyBy('id');

        foreach ($cart as $id => $item) {
            if (isset($articles[$id])) {
                $article = $articles[$id];
                $quantity = $item['quantity'];
                // Ensure quantity does not exceed available stock before checkout
                if ($quantity > $article->quantite) {
                    // Optionally, adjust quantity in cart here and notify user
                    // For now, redirect back with an error
                    return redirect()->route('cart.index')->with('error', "Quantity for article '{$article->name}' exceeds available stock. Please update your cart.");
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

        // If any item was removed because it's no longer available or IDs mismatch
        if (count($articlesInCart) !== count($cart)) {
             // Re-calculate cart if some items were invalid / removed
            $validCart = [];
            foreach($articlesInCart as $validItem) {
                $validCart[$validItem['id']] = ['quantity' => $validItem['quantity']];
            }
            Session::put('cart', $validCart);
            // if all items became invalid
            if(empty($articlesInCart)){
                 return redirect()->route('products.index')->with('info', 'Some items in your cart are no longer available. Your cart has been updated.');
            }
             return redirect()->route('cart.index')->with('info', 'Some items in your cart were updated. Please review before checkout.');
        }


        return view('checkout.index', compact('articlesInCart', 'totalPrice'));
    }

    /**
     * Process the checkout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index')->with('info', 'Your cart is empty.');
        }

        // Validate submitted information
        $validator = Validator::make($request->all(), [
            'shipping_name' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:255',
            'billing_same_as_shipping' => 'nullable|boolean',
            'billing_name' => 'required_if:billing_same_as_shipping,false|nullable|string|max:255',
            'billing_address' => 'required_if:billing_same_as_shipping,false|nullable|string|max:255',
            'billing_city' => 'required_if:billing_same_as_shipping,false|nullable|string|max:255',
            'billing_postal_code' => 'required_if:billing_same_as_shipping,false|nullable|string|max:20',
            'billing_country' => 'required_if:billing_same_as_shipping,false|nullable|string|max:255',
            'payment_method' => 'required|string', // Placeholder for payment method
        ]);

        if ($validator->fails()) {
            return redirect()->route('checkout.index')
                        ->withErrors($validator)
                        ->withInput();
        }

        // --- Mock Payment Processing ---
        $paymentSuccessful = true; // Simulate payment success

        if (!$paymentSuccessful) {
            return redirect()->route('checkout.index')->with('error', 'Payment failed. Please try again.');
        }

        // --- Create Order ---
        DB::beginTransaction();
        try {
            $totalPrice = 0;
            $articleDetailsForOrder = [];
            $articleIds = array_keys($cart);
            $articlesFromDb = Article::whereIn('id', $articleIds)->lockForUpdate()->get()->keyBy('id'); // Lock for update to prevent race conditions on quantity

            foreach ($cart as $id => $item) {
                if (!isset($articlesFromDb[$id])) {
                    throw new \Exception("Article with ID {$id} not found.");
                }
                $article = $articlesFromDb[$id];
                $requestedQuantity = $item['quantity'];

                if ($article->quantite < $requestedQuantity) {
                    throw new \Exception("Not enough stock for article '{$article->name}'. Requested: {$requestedQuantity}, Available: {$article->quantite}.");
                }
                $subtotal = $article->prix * $requestedQuantity;
                $totalPrice += $subtotal;
                $articleDetailsForOrder[$id] = [
                    'article' => $article,
                    'quantity' => $requestedQuantity,
                    'price' => $article->prix,
                ];
            }

            $shippingDetails = [
                'name' => $request->shipping_name,
                'address' => $request->shipping_address,
                'city' => $request->shipping_city,
                'postal_code' => $request->shipping_postal_code,
                'country' => $request->shipping_country,
            ];

            $billingDetails = $shippingDetails; // Default to same as shipping
            if (!$request->boolean('billing_same_as_shipping')) {
                $billingDetails = [
                    'name' => $request->billing_name,
                    'address' => $request->billing_address,
                    'city' => $request->billing_city,
                    'postal_code' => $request->billing_postal_code,
                    'country' => $request->billing_country,
                ];
            }

            $order = Order::create([
                'user_id' => auth()->id(),
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
                'status' => 'pending_payment', // Or 'processing' if payment is confirmed
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending', // Will be 'paid' after successful payment
            ]);

            foreach ($articleDetailsForOrder as $id => $details) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'article_id' => $id,
                    'quantity' => $details['quantity'],
                    'price' => $details['price'],
                ]);
                // Decrease stock
                $details['article']->quantite -= $details['quantity'];
                $details['article']->save();
            }

            // If payment was truly successful (this is still mock)
            if ($paymentSuccessful) {
                $order->status = 'processing';
                $order->payment_status = 'paid';
                $order->save();
            }

            DB::commit();
            // Clear the cart
            Session::forget('cart');
            // Redirect to an order success page or user's order history
            return redirect()->route('home')->with('success', 'Order placed successfully! Order ID: ' . $order->id); // Consider an order confirmation page

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error: Log::error('Order processing failed: ' . $e->getMessage());
            return redirect()->route('checkout.index')->with('error', 'Order processing failed: ' . $e->getMessage())->withInput();
        }
    }
}
