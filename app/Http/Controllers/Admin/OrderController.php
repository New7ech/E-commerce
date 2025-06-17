<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Contrôleur pour la gestion des commandes dans la section d'administration.
 * Permet de visualiser et de mettre à jour les commandes.
 */
class OrderController extends Controller
{
    /**
     * Affiche une liste paginée des commandes avec options de filtrage et de recherche.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $status = $request->input('status'); // Récupère le statut pour filtrer
        $search = $request->input('search'); // Récupère le terme de recherche (ID commande, nom/email utilisateur)

        $orders = Order::with('user') // Charge la relation utilisateur pour éviter les requêtes N+1
            ->when($status, function ($query, $status) {
                // Filtre par statut si un statut est fourni
                return $query->where('status', $status);
            })
            ->when($search, function ($query, $searchTerm) {
                // Recherche par ID de commande ou dans les informations de l'utilisateur (nom, email)
                return $query->where('id', 'like', "%{$searchTerm}%")
                             ->orWhereHas('user', function ($q) use ($searchTerm) {
                                 $q->where('name', 'like', "%{$searchTerm}%")
                                   ->orWhere('email', 'like', "%{$searchTerm}%");
                             });
            })
            ->latest() // Trie les commandes par date de création (les plus récentes d'abord)
            ->paginate(15); // Pagine les résultats

        return view('admin.orders.index', compact('orders', 'status', 'search'));
    }

    /**
     * Affiche les détails d'une commande spécifique.
     *
     * @param  \App\Models\Order  $order La commande à afficher.
     * @return \Illuminate\View\View
     */
    public function show(Order $order): \Illuminate\View\View
    {
        $order->load('items.article', 'user'); // Charge les relations articles de la commande et utilisateur
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Met à jour le statut d'une commande spécifique.
     *
     * @param  \Illuminate\Http\Request  $request Les données de la requête contenant le nouveau statut.
     * @param  \App\Models\Order  $order La commande à mettre à jour.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending_payment,processing,shipped,delivered,cancelled,refunded',
            // Ajouter d'autres statuts valides au besoin
        ]);

        $order->status = $request->status;
        // Potentiellement, mettre à jour aussi payment_status en fonction du nouveau statut de la commande
        if (in_array($request->status, ['cancelled', 'refunded']) && $order->payment_status === 'paid') {
            // Ceci est un exemple simplifié. Une logique de remboursement réelle impliquerait l'API de la passerelle de paiement.
            // $order->payment_status = 'refunded'; // Ou un autre statut approprié
        }
        $order->save();

        return redirect()->route('admin.orders.show', $order)->with('success', 'Le statut de la commande a été mis à jour avec succès.');
    }
}
