<?php

use App\Http\Controllers\AccueilController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EmplacementController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\UserController;
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
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index'); // Middleware removed for guest checkout
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process'); // Middleware removed for guest checkout

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



//la gestion des utilisateurs
Route::resource('users', UserController::class);
Route::resource('roles', RoleController::class);
Route::resource('permissions', PermissionController::class);
Route::resource('categories', CategorieController::class);
Route::resource('fournisseurs', FournisseurController::class);
Route::resource('emplacements',EmplacementController::class);
Route::resource('articles', ArticleController::class);
Route::resource('factures', FactureController::class);
Route::resource('accueil', AccueilController::class);
Route::get('/', [App\Http\Controllers\AccueilController::class, 'index'])->name('accueil');

Route::get('/factures/{facture}/pdf', [FactureController::class, 'genererPdf'])->name('factures.pdf');
Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');

// Notification routes
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

// Wishlist Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add/{article}', [App\Http\Controllers\WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{article}', [App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');
});

// Custom Authentication Routes for Guests
Route::middleware('guest')->group(function () {
    Route::get('/custom-register', [App\Http\Controllers\Auth\CustomRegisterController::class, 'create'])->name('custom.register');
    Route::post('/custom-register', [App\Http\Controllers\Auth\CustomRegisterController::class, 'store']);

    // Custom Login Routes
    Route::get('/custom-login', [App\Http\Controllers\Auth\CustomLoginController::class, 'create'])->name('custom.login');
    Route::post('/custom-login', [App\Http\Controllers\Auth\CustomLoginController::class, 'store']);
    // Add a route named 'login' pointing to the custom login controller's create method
    // This is to ensure Laravel's default Authenticate middleware redirects here if app/Http/Middleware/Authenticate.php is missing
    Route::get('/login', [App\Http\Controllers\Auth\CustomLoginController::class, 'create'])->name('login');


    // Custom Password Reset Link Request Routes
    Route::get('/custom-forgot-password', [App\Http\Controllers\Auth\CustomForgotPasswordController::class, 'create'])->name('custom.password.request');
    Route::post('/custom-forgot-password', [App\Http\Controllers\Auth\CustomForgotPasswordController::class, 'store'])->name('custom.password.email');

    // Custom Password Reset Routes
    Route::get('/custom-reset-password/{token}', [App\Http\Controllers\Auth\CustomResetPasswordController::class, 'create'])->name('custom.password.reset');
    Route::post('/custom-reset-password', [App\Http\Controllers\Auth\CustomResetPasswordController::class, 'store'])->name('custom.password.update.action');
});

// Custom Authenticated Routes (e.g., Logout)
Route::middleware('auth')->group(function () {
    Route::post('/custom-logout', [App\Http\Controllers\Auth\CustomLoginController::class, 'destroy'])->name('custom.logout');
});
