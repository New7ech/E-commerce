<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search'); // Search by Order ID or User Name/Email

        $orders = Order::with('user')
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($search, function ($query, $searchTerm) {
                return $query->where('id', 'like', "%{$searchTerm}%")
                             ->orWhereHas('user', function ($q) use ($searchTerm) {
                                 $q->where('name', 'like', "%{$searchTerm}%")
                                   ->orWhere('email', 'like', "%{$searchTerm}%");
                             });
            })
            ->latest()
            ->paginate(15);

        return view('admin.orders.index', compact('orders', 'status', 'search'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        $order->load('items.article', 'user'); // Eager load relationships
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the status of the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending_payment,processing,shipped,delivered,cancelled,refunded',
            // Add more valid statuses as needed
        ]);

        $order->status = $request->status;
        // Potentially update payment_status as well based on the new order status
        if (in_array($request->status, ['cancelled', 'refunded']) && $order->payment_status === 'paid') {
            // This is a simplified example. Actual refund logic would involve payment gateway API.
            // $order->payment_status = 'refunded'; // Or some other appropriate status
        }
        $order->save();

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated successfully.');
    }
}
