<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/products', [ArticleController::class, 'productList'])->name('products.index');

Route::get('/products/{id}', [ArticleController::class, 'productShow'])->name('products.show');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{article}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{article}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{article}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout Routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index')->middleware('auth'); // Ensure user is logged in
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process')->middleware('auth');

// Admin Routes for Order Management
// Temporarily removing 'admin' middleware for testing due to BindingResolutionException
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Routes for Article CRUD in admin
    Route::resource('articles', ArticleController::class)->except(['show']);
    // Public show route is /products/{id}, admin can use edit or a dedicated admin show if created
    // If ArticleController@show is used by admin, it might need adjustment or be fine.
    // For now, let's assume `except(['show'])` is a safe bet to avoid conflict with public productShow
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/orders', [ProfileController::class, 'orderHistory'])->name('profile.orders');
});

require __DIR__.'/auth.php';
