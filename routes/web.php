<?php

use App\Http\Controllers\AccueilController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\CustomForgotPasswordController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\CustomRegisterController;
use App\Http\Controllers\Auth\CustomResetPasswordController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController; // Mise à jour du nom du contrôleur
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
use App\Http\Controllers\WishlistController;
use App\Models\Article;
use App\Models\Categorie;
use App\Models\User as UserModel; // Alias User model to avoid conflict with UserController
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordController; // Added import for PasswordController
















Route::get('/products', [ArticleController::class, 'productList'])->name('products.index');

// Modifié pour utiliser la méthode 'show' existante et l'injection de modèle implicite
Route::get('/products/{article}', [ArticleController::class, 'show'])->name('products.show');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::patch('/cart/update/{article}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{article}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout Routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index'); // Middleware removed for guest checkout
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process'); // Middleware removed for guest checkout

// Admin Routes for Order Management
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Routes for Article CRUD in admin
    Route::resource('articles', ArticleController::class)->except(['show']);
    // Public show route is /products/{id}, admin can use edit or a dedicated admin show if created
    // If ArticleController@show is used by admin, it might need adjustment or be fine.
    // For now, let's assume `except(['show'])` is a safe bet to avoid conflict with public productShow

    // Moved resource routes
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('articles', ArticleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('categories', CategoryController::class); // Mise à jour du contrôleur
    Route::resource('fournisseurs', FournisseurController::class);
    Route::resource('emplacements', EmplacementController::class);
    Route::resource('factures', FactureController::class);

    // Moved individual routes
    Route::get('/factures/{facture}/pdf', [FactureController::class, 'genererPdf'])->name('factures.pdf');
    Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');
});

// Route publique pour afficher les produits d'une catégorie
Route::get('/category/{category:slug}', [CategoryController::class, 'showPublic'])->name('public.categories.show'); // Mise à jour

Route::get('/dashboard', function () {
    $productCount = Article::count();
    $categoryCount = Categorie::count();
    $userCount = UserModel::count(); // Use the aliased UserModel

    $recentlyUpdatedProducts = Article::with('categorie')
                                    ->orderBy('updated_at', 'desc')
                                    ->take(4)
                                    ->get();

    $newArrivals = Article::with('categorie')
                            ->orderBy('created_at', 'desc')
                            ->take(4)
                            ->get();

    return view('dashboard', compact(
        'productCount',
        'categoryCount',
        'userCount',
        'recentlyUpdatedProducts',
        'newArrivals'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/orders', [ProfileController::class, 'orderHistory'])->name('profile.orders');
    Route::put('user/password', [PasswordController::class, 'update'])->name('password.update');
});

// require __DIR__.'/auth.php'; // Les routes d'authentification Breeze par défaut sont désactivées au profit des routes personnalisées.

// Nouvelle route pour la page d'accueil
Route::get('/', [ArticleController::class, 'welcome'])->name('homepage');


// Notification routes
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

// Wishlist Routes / Add to Cart (Authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add/{article}', [App\Http\Controllers\WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{article}', [App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');

    Route::post('/cart/add/{article}', [CartController::class, 'add'])->name('cart.add'); // Moved here
});

// Custom Authentication Routes for Guests
Route::middleware('guest')->group(function () {
    Route::get('/custom-register', [CustomRegisterController::class, 'create'])->name('custom.register');
    Route::post('/custom-register', [CustomRegisterController::class, 'store']);

    // Custom Login Routes
    Route::get('/custom-login', [CustomLoginController::class, 'create'])->name('custom.login');
    Route::post('/custom-login', [CustomLoginController::class, 'store']);
    // Add a route named 'login' pointing to the custom login controller's create method
    // This is to ensure Laravel's default Authenticate middleware redirects here if app/Http/Middleware/Authenticate.php is missing
    Route::get('/login', [CustomLoginController::class, 'create'])->name('login');


    // Custom Password Reset Link Request Routes
    Route::get('/custom-forgot-password', [CustomForgotPasswordController::class, 'create'])->name('custom.password.request');
    Route::post('/custom-forgot-password', [CustomForgotPasswordController::class, 'store'])->name('custom.password.email');

    // Custom Password Reset Routes
    Route::get('/custom-reset-password/{token}', [CustomResetPasswordController::class, 'create'])->name('custom.password.reset');
    Route::post('/custom-reset-password', [CustomResetPasswordController::class, 'store'])->name('custom.password.update.action');
});

// Custom Authenticated Routes (e.g., Logout)
Route::middleware('auth')->group(function () {
    Route::post('/custom-logout', [CustomLoginController::class, 'destroy'])->name('custom.logout');
});

// Routes pour les pages statiques
use App\Http\Controllers\StaticPageController;
Route::get('/mentions-legales', [StaticPageController::class, 'mentionsLegales'])->name('static.mentions-legales');
Route::get('/conditions-generales-de-vente', [StaticPageController::class, 'cgv'])->name('static.cgv');
Route::get('/politique-de-confidentialite', [StaticPageController::class, 'politiqueConfidentialite'])->name('static.politique-confidentialite');
Route::get('/contactez-nous', [StaticPageController::class, 'contact'])->name('static.contact');
Route::get('/promotions', [StaticPageController::class, 'promotions'])->name('static.promotions');
