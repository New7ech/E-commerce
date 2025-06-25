<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bienvenue chez Nous - Votre E-commerce Burkinabé</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>

    <!-- Google Fonts: Nunito -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Schema.org Markup -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Store",
      "name": "Votre E-commerce Burkinabé",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('path/to/your/logo.png') }}", // Mettez le vrai chemin vers votre logo
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "123 Rue de Ouaga",
        "addressLocality": "Ouagadougou",
        "postalCode": "01 BP 1234",
        "addressCountry": "BF"
      },
      "telephone": "+226 XX XX XX XX",
      "priceRange": "FCFA5000 - FCFA500000"
    }
    </script>

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            /* Couleurs principales en variables CSS pour une utilisation facile */
            --color-primary-orange: #F36F21;
            --color-primary-green: #2E8B57;
            --color-primary-gold: #FFD700;
        }
        .bg-primary-orange { background-color: var(--color-primary-orange); }
        .text-primary-orange { color: var(--color-primary-orange); }
        .border-primary-orange { border-color: var(--color-primary-orange); }

        .bg-primary-green { background-color: var(--color-primary-green); }
        .text-primary-green { color: var(--color-primary-green); }

        .bg-primary-gold { background-color: var(--color-primary-gold); }
        .text-primary-gold { color: var(--color-primary-gold); }

        /* Styles pour le carrousel (simplifié) */
        .carousel { display: flex; overflow-x: auto; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; }
        .carousel-item { flex: 0 0 100%; scroll-snap-align: start; }
        .carousel::-webkit-scrollbar { display: none; } /* Masquer la barre de défilement */

        /* Animations subtiles */
        .hover-scale { transition: transform 0.3s ease-in-out; }
        .hover-scale:hover { transform: scale(1.05); }

        /* Dark mode (placeholder, sera activé par JS) */
        .dark body { background-color: #1a202c; color: #e2e8f0; }
        .dark .bg-white { background-color: #2d3748; }
        .dark .text-gray-800 { color: #e2e8f0; }
        .dark .text-gray-600 { color: #a0aec0; }
        .dark .border-gray-200 { border-color: #4a5568; }
        /* Ajoutez d'autres styles dark mode au besoin */

        /* Style pour le toast (caché par défaut) */
        .toast {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 10px 20px;
            background-color: rgba(0,0,0,0.75);
            color: white;
            border-radius: 8px;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            pointer-events: none;
        }
        .toast.show {
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <!-- Logo et Badge -->
                <div class="flex items-center mb-3 md:mb-0">
                    <a href="{{ route('homepage') }}" class="text-2xl font-bold text-primary-orange" data-testid="logo">
                        LOGO<span class="text-primary-green">Shop</span>
                    </a>
                    <span class="ml-3 bg-primary-gold text-white text-xs font-semibold px-2 py-1 rounded" data-testid="badge-burkinabe">
                        100% Burkinabé
                    </span>
                </div>

                <!-- Barre de recherche -->
                <div class="w-full md:w-1/2 lg:w-1/3 mb-3 md:mb-0 relative">
                    <input type="text" placeholder="Rechercher un produit, une marque ou une catégorie..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-orange text-gray-900 dark:text-gray-100 dark:bg-gray-700 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400"
                           data-testid="search-bar">
                    <!-- Suggestions dynamiques (à implémenter avec JS) -->
                    <div class="absolute left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-10 hidden dark:bg-gray-700 dark:border-gray-600" id="search-suggestions">
                        <!-- item: <a href="#" class="block px-4 py-2 hover:bg-gray-100">Suggestion 1</a> -->
                    </div>
                </div>

                <!-- Icônes utilisateur, panier, favoris -->
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('custom.login') }}" class="text-gray-600 hover:text-primary-orange" aria-label="Mon compte" data-testid="icon-user-login">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </a>
                    @else
                        <a href="{{ route('profile.edit') }}" class="text-gray-600 hover:text-primary-orange" aria-label="Mon compte" data-testid="icon-user-profile">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </a>
                        <form method="POST" action="{{ route('custom.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-primary-orange" aria-label="Déconnexion" data-testid="icon-user-logout">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    @endguest
                    <a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-primary-orange relative" aria-label="Panier" data-testid="icon-cart">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span class="absolute -top-2 -right-2 bg-primary-orange text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" data-testid="cart-count">0</span> <!-- Mettre à jour dynamiquement -->
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="text-gray-600 hover:text-primary-orange" aria-label="Favoris" data-testid="icon-wishlist">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </a>
                </div>
            </div>

            <!-- Menu catégories -->
            <nav class="mt-3 border-t border-gray-200 pt-3">
                <ul class="flex flex-wrap justify-center md:justify-start space-x-4 md:space-x-6">
                    @if(isset($categories) && $categories->count() > 0)
                        @foreach($categories as $category)
                            @if($category && !empty($category->slug)) {{-- Vérification supplémentaire --}}
                                <li><a href="{{ route('public.categories.show', $category) }}" class="text-gray-700 hover:text-primary-orange font-semibold" data-testid="category-link-{{ $category->slug }}">{{ $category->name }}</a></li>
                            @endif
                        @endforeach
                    @endif
                     <li><a href="{{ route('static.promotions') }}" class="text-gray-700 hover:text-primary-orange font-semibold" data-testid="promotions-link">Promotions</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">

        <!-- Hero Section -->
        <section class="mb-12" data-testid="hero-section">
            <!-- Carrousel -->
            <div class="relative overflow-hidden rounded-lg shadow-lg mb-6 h-64 md:h-96 carousel" id="hero-carousel">
                <!-- Slide 1 (Électronique) -->
                <div class="carousel-item min-w-full bg-primary-orange text-white flex items-center justify-center p-8">
                    <div class="text-center">
                        <h2 class="text-3xl md:text-5xl font-bold mb-4">Promo Incroyable!</h2>
                        <p class="text-lg md:text-xl mb-6">Jusqu'à -50% sur une sélection d'articles Électroniques.</p>
                        @php $electroniqueCategory = $categories->firstWhere('slug', 'electronique'); @endphp
                        <a href="{{ $electroniqueCategory ? route('public.categories.show', $electroniqueCategory) : route('static.promotions') }}" class="bg-white text-primary-orange font-bold py-3 px-8 rounded-lg hover:bg-opacity-90 transition duration-300 text-lg" data-testid="cta-hero-1">
                            Acheter maintenant &rarr;
                        </a>
                    </div>
                </div>
                <!-- Slide 2 (Mode) -->
                <div class="carousel-item min-w-full bg-primary-green text-white flex items-center justify-center p-8">
                     <div class="text-center">
                        <h2 class="text-3xl md:text-5xl font-bold mb-4">Nouvelle Collection Mode</h2>
                        <p class="text-lg md:text-xl mb-6">Découvrez les dernières tendances Faso Danfani.</p>
                        @php $modeCategory = $categories->firstWhere('slug', 'mode'); @endphp
                        <a href="{{ $modeCategory ? route('public.categories.show', $modeCategory) : route('static.promotions') }}" class="bg-white text-primary-green font-bold py-3 px-8 rounded-lg hover:bg-opacity-90 transition duration-300 text-lg" data-testid="cta-hero-2">
                            Explorer &rarr;
                        </a>
                    </div>
                </div>
                <!-- Slide 3 (Agroalimentaire) -->
                <div class="carousel-item min-w-full bg-primary-gold text-gray-800 flex items-center justify-center p-8">
                     <div class="text-center">
                        <h2 class="text-3xl md:text-5xl font-bold mb-4">Produits du Terroir Frais</h2>
                        <p class="text-lg md:text-xl mb-6">Directement des producteurs locaux à votre table.</p>
                        @php $agroCategory = $categories->firstWhere('slug', 'agroalimentaire'); @endphp
                        <a href="{{ $agroCategory ? route('public.categories.show', $agroCategory) : route('static.promotions') }}" class="bg-gray-800 text-white font-bold py-3 px-8 rounded-lg hover:bg-opacity-90 transition duration-300 text-lg" data-testid="cta-hero-3">
                            Commander &rarr;
                        </a>
                    </div>
                </div>
            </div>
             <!-- Carrousel indicateurs -->
            <div class="flex justify-center space-x-2" id="carousel-indicators">
                <!-- Les indicateurs seront générés par JS -->
            </div>


            <!-- Bannière Livraison -->
            <div class="bg-primary-green text-white text-center py-3 px-4 rounded-lg shadow" data-testid="banner-livraison">
                <p class="font-semibold">Livraison OFFERTE à Ouagadougou pour toute commande supérieure à 50.000 FCFA!</p>
            </div>
        </section>

        <!-- Section Produits -->
        <section class="mb-12" data-testid="products-section">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Nos Produits</h2>

            <!-- Filtres dynamiques (Interface) -->
            <div class="mb-6 flex flex-wrap gap-2 items-center">
                <span class="font-semibold mr-2">Filtrer par:</span>
                <select class="px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-orange" data-testid="filter-sort">
                    <option value="popular">Plus vendus</option>
                    <option value="newest">Nouveautés</option>
                    <option value="price_asc">Prix croissant</option>
                    <option value="price_desc">Prix décroissant</option>
                </select>
                <select class="px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-orange" data-testid="filter-category">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <!-- Autres filtres possibles (prix, etc.) -->
            </div>

            <!-- Grille de produits -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($articles as $article)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover-scale product-card" data-product-id="{{ $article->id }}" data-testid="product-card-{{ $article->id }}">
                    <a href="{{ route('products.show', $article) }}">
                        <img src="{{ $article->image_url ?? 'https://via.placeholder.com/300x200?text=Produit' }}" alt="{{ $article->title }}" class="w-full h-48 object-cover">
                    </a>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1 truncate" title="{{ $article->title }}">
                            <a href="{{ route('products.show', $article) }}">{{ $article->title }}</a>
                        </h3>
                        <p class="text-sm text-gray-500 mb-2">{{ $article->category->name ?? 'Non catégorisé' }}</p>
                        <div class="flex items-baseline mb-2">
                            <span class="text-xl font-bold text-primary-orange">
                                {{ number_format($article->promo_price ?? $article->price, 0, ',', ' ') }} FCFA
                            </span>
                            @if($article->promo_price)
                            <span class="ml-2 text-sm text-gray-500 line-through">
                                {{ number_format($article->price, 0, ',', ' ') }} FCFA
                            </span>
                            @endif
                        </div>

                        @if($article->available_for_click_and_collect)
                        <span class="inline-block bg-green-100 text-primary-green text-xs font-semibold px-2 py-1 rounded-full mb-2" data-testid="badge-click-collect">
                            Click & Collect disponible
                        </span>
                        @endif
                         <!-- Rating (exemple statique) -->
                        @if($article->rating)
                        <div class="flex items-center mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($article->rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.376 2.454a1 1 0 00-.364 1.118l1.287 3.966c.3.921-.755 1.688-1.54 1.118l-3.375-2.454a1 1 0 00-1.176 0l-3.375 2.454c-.784.57-1.838-.197-1.539-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.04 9.393c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69L9.049 2.927z"/>
                                </svg>
                            @endfor
                            <span class="ml-1 text-xs text-gray-500">({{ $article->rating }}/5)</span>
                        </div>
                        @endif

                        <button class="w-full bg-primary-orange text-white py-2 rounded-lg hover:bg-opacity-90 transition duration-300 add-to-cart-btn" data-testid="btn-add-cart-{{ $article->id }}">
                            Ajouter au panier
                        </button>
                    </div>
                </div>
                @empty
                <p class="col-span-full text-center text-gray-500">Aucun produit trouvé pour le moment.</p>
                @endforelse
            </div>
             <!-- Pagination -->
            @if ($articles->hasPages())
            <div class="mt-8" data-testid="pagination-links">
                {{ $articles->links() }} <!-- Utilise les vues de pagination par défaut de Laravel, stylées par Tailwind si configuré -->
            </div>
            @endif
        </section>

        <!-- Section Paiements -->
        <section class="mb-12 bg-white p-6 rounded-lg shadow-md" data-testid="payment-section">
            <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">Paiement 100% Sécurisé</h2>
            <div class="flex flex-wrap justify-center items-center gap-4 md:gap-8 mb-4">
                <img src="https://via.placeholder.com/100x50?text=Orange+Money" alt="Orange Money" class="h-10 md:h-12" data-testid="logo-orange-money">
                <img src="https://via.placeholder.com/100x50?text=Moov+Money" alt="Moov Money" class="h-10 md:h-12" data-testid="logo-moov-money">
                <img src="https://via.placeholder.com/80x50?text=Visa" alt="Visa" class="h-8 md:h-10" data-testid="logo-visa">
                <img src="https://via.placeholder.com/100x50?text=Mastercard" alt="Mastercard" class="h-8 md:h-10" data-testid="logo-mastercard">
                <!-- Ajoutez d'autres logos ici -->
            </div>
            <div class="text-center text-sm text-primary-green flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                </svg>
                <span>Transactions sécurisées par cryptage SSL</span>
            </div>
        </section>

        <!-- Sections Témoignages & Engagements Locaux -->
        <section class="mb-12 grid md:grid-cols-2 gap-8" data-testid="testimonials-engagement-section">
            <!-- Témoignage Client -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Ce que disent nos clients</h3>
                <div class="border-l-4 border-primary-orange pl-4">
                    <p class="italic text-gray-700">"Reçu ma commande en 24h à Ouaga ! Service client au top et produits de qualité. Je recommande vivement !"</p>
                    <p class="mt-2 font-semibold text-gray-600">- Aïssata K.</p>
                </div>
                <div class="mt-4 text-center">
                    <span class="text-3xl font-bold text-primary-green" id="satisfied-clients-counter" data-testid="counter-clients">+1278</span>
                    <p class="text-gray-600">clients satisfaits</p>
                </div>
            </div>

            <!-- Engagements Locaux & Points de Retrait -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Nos Engagements Locaux</h3>
                <p class="text-gray-700 mb-2">Nous soutenons les producteurs et artisans du Burkina Faso.</p>
                <div class="mb-4">
                    <h4 class="font-semibold text-primary-green mb-2">Points de retrait à Ouagadougou:</h4>
                    <!-- Placeholder pour carte interactive -->
                    <div class="bg-gray-200 h-48 rounded flex items-center justify-center text-gray-500" data-testid="map-placeholder">
                        [Carte interactive des points de retrait ici]
                    </div>
                </div>
            </div>
        </section>

        <!-- Section "Produits du terroir" -->
        <section class="mb-12" data-testid="terroir-section">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Spécial Produits du Terroir</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Exemple produit terroir 1 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover-scale">
                    <img src="https://via.placeholder.com/300x200?text=Karité+Bio" alt="Beurre de Karité Bio" class="w-full h-40 object-cover">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Beurre de Karité Bio</h3>
                        <p class="text-sm text-gray-500 mb-2">Non raffiné, 100% naturel</p>
                        <span class="text-lg font-bold text-primary-orange">3.500 FCFA</span>
                    </div>
                </div>
                <!-- Exemple produit terroir 2 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover-scale">
                    <img src="https://via.placeholder.com/300x200?text=Riz+de+Bagré" alt="Riz de Bagré" class="w-full h-40 object-cover">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Riz de Bagré Parfumé</h3>
                        <p class="text-sm text-gray-500 mb-2">Sack de 5kg</p>
                        <span class="text-lg font-bold text-primary-orange">7.000 FCFA</span>
                    </div>
                </div>
                <!-- Exemple produit terroir 3 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover-scale">
                     <img src="https://via.placeholder.com/300x200?text=Faso+Danfani" alt="Tissu Faso Danfani" class="w-full h-40 object-cover">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Tissu Faso Danfani</h3>
                        <p class="text-sm text-gray-500 mb-2">Authentique, tissé à la main</p>
                        <span class="text-lg font-bold text-primary-orange">15.000 FCFA / pagne</span>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white pt-10 pb-6" data-testid="footer">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <!-- A propos -->
                <div>
                    <h5 class="text-lg font-semibold mb-3">À Propos de Nous</h5>
                    <p class="text-sm text-gray-400 mb-2">Votre boutique en ligne 100% Burkinabé, engagée pour la qualité et le service local.</p>
                     <div class="flex items-center mt-3" data-testid="cnp-agrément">
                        <img src="https://via.placeholder.com/50x50?text=CNP" alt="Logo CNP Burkina Faso" class="h-10 mr-2"> <!-- Placeholder logo CNP -->
                        <span class="text-xs text-gray-300">Agréé CNP du Burkina Faso</span>
                    </div>
                </div>

                <!-- Liens utiles -->
                <div>
                    <h5 class="text-lg font-semibold mb-3">Liens Utiles</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('static.mentions-legales') }}" class="text-gray-400 hover:text-primary-orange">Mentions Légales</a></li>
                        <li><a href="{{ route('static.cgv') }}" class="text-gray-400 hover:text-primary-orange">Conditions Générales de Vente</a></li>
                        <li><a href="{{ route('static.politique-confidentialite') }}" class="text-gray-400 hover:text-primary-orange">Politique de Confidentialité</a></li>
                        <li><a href="{{ route('static.contact') }}" class="text-gray-400 hover:text-primary-orange">Contactez-nous</a></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h5 class="text-lg font-semibold mb-3">Newsletter</h5>
                    <p class="text-sm text-gray-400 mb-2">Recevez nos meilleures offres et nouveautés.</p>
                    <form action="#" method="POST" class="mt-2"> {{-- TODO: Ajouter une route pour la soumission de la newsletter --}}
                        <input type="email" placeholder="Votre email" class="w-full p-2 rounded-md text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-orange mb-2 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400" data-testid="newsletter-email" name="email">
                        <button type="submit" class="w-full bg-primary-orange hover:bg-opacity-90 text-white py-2 rounded-md font-semibold" data-testid="newsletter-submit">S'inscrire</button>
                    </form>
                </div>

                <!-- Réseaux sociaux et Icônes locales -->
                <div>
                    <h5 class="text-lg font-semibold mb-3">Suivez-nous</h5>
                    <div class="flex space-x-4 mb-4">
                        {{-- Remplacer # par les vrais liens de réseaux sociaux --}}
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-primary-orange" aria-label="Facebook"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-primary-orange" aria-label="Instagram"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.356.2-6.784 2.624-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.356 2.624 6.784 6.98 6.98 1.281.059 1.689.073 4.948.073 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.624 6.979-6.98.059-1.28.073-1.689.073-4.948s-.014-3.667-.072-4.947c-.196-4.354-2.624-6.782-6.979-6.98-1.281-.059-1.69-.073-4.948-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                        <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-primary-orange" aria-label="Twitter"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-.424.727-.666 1.581-.666 2.477 0 1.61.823 3.027 2.071 3.858-.764-.024-1.482-.232-2.11-.583v.06c0 2.256 1.606 4.135 3.737 4.568-.39.106-.803.162-1.227.162-.3 0-.593-.028-.877-.082.593 1.85 2.313 3.198 4.352 3.234-1.595 1.25-3.604 1.995-5.786 1.995-.376 0-.747-.022-1.112-.065 2.062 1.323 4.51 2.093 7.14 2.093 8.57 0 13.255-7.098 13.255-13.254 0-.202-.005-.403-.014-.602.91-.658 1.7-1.475 2.323-2.408z"/></svg></a>
                    </div>
                    <h5 class="text-md font-semibold mb-2 text-gray-300">Nos valeurs locales :</h5>
                    <div class="flex space-x-3 items-center text-gray-400">
                        <!-- Icônes personnalisées (placeholders, remplacer par vrais SVG/images) -->
                        <span title="Riz Local" data-testid="icon-riz">🌾</span>
                        <span title="Karité Naturel" data-testid="icon-karite">🌰</span>
                        <span title="Faso Danfani Authentique" data-testid="icon-faso-danfani">🧵</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 pt-6 text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} Votre E-commerce Burkinabé. Tous droits réservés.
            </div>
        </div>
    </footer>

    <!-- Toast pour notifications (ex: "12 personnes regardent ce produit") -->
    <div id="product-view-toast" class="toast" data-testid="toast-notification">
        <!-- Le message sera inséré par JS -->
    </div>

    <!-- Scripts JS Optimisés -->
    <script>
        // Mode économie de données (placeholder, logique à ajouter si besoin)
        const isDataSaverEnabled = () => {
            return navigator.connection && navigator.connection.saveData;
        };

        // Dark Mode automatique
        const applyDarkMode = () => {
            const hour = new Date().getHours();
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            // Activer le mode sombre entre 19h et 6h, ou si l'utilisateur préfère le mode sombre
            if ((hour >= 19 || hour < 6) || prefersDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        };
        applyDarkMode();
        setInterval(applyDarkMode, 60 * 60 * 1000); // Vérifier toutes les heures

        // Carrousel Hero Section
        const carousel = document.getElementById('hero-carousel');
        const carouselItems = carousel.querySelectorAll('.carousel-item');
        const indicatorsContainer = document.getElementById('carousel-indicators');
        let currentSlide = 0;
        let slideInterval;

        function updateCarouselIndicators() {
            indicatorsContainer.innerHTML = '';
            carouselItems.forEach((_, index) => {
                const button = document.createElement('button');
                button.classList.add('w-3', 'h-3', 'rounded-full', 'transition-colors', 'duration-300');
                if (index === currentSlide) {
                    button.classList.add('bg-primary-orange');
                } else {
                    button.classList.add('bg-gray-300', 'hover:bg-gray-400');
                }
                button.addEventListener('click', () => {
                    goToSlide(index);
                    resetSlideInterval();
                });
                indicatorsContainer.appendChild(button);
            });
        }

        function goToSlide(slideIndex) {
            carousel.scrollTo({
                left: carousel.offsetWidth * slideIndex,
                behavior: 'smooth'
            });
            currentSlide = slideIndex;
            updateCarouselIndicators();
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % carouselItems.length;
            goToSlide(currentSlide);
        }

        function startSlideInterval() {
            slideInterval = setInterval(nextSlide, 5000); // Change de slide toutes les 5 secondes
        }

        function resetSlideInterval() {
            clearInterval(slideInterval);
            startSlideInterval();
        }

        if (carousel && carouselItems.length > 0) {
            updateCarouselIndicators();
            startSlideInterval();
             // Optionnel: arrêter le carrousel au survol
            carousel.addEventListener('mouseenter', () => clearInterval(slideInterval));
            carousel.addEventListener('mouseleave', startSlideInterval);
        }


        // Compteur dynamique clients satisfaits (simulation d'animation)
        const clientsCounter = document.getElementById('satisfied-clients-counter');
        if (clientsCounter) {
            let count = 0;
            const target = 1278;
            const duration = 2000; // 2 secondes
            const stepTime = Math.abs(Math.floor(duration / target));

            const timer = setInterval(() => {
                count += Math.ceil(target / (duration / 50)); // Augmente plus vite au début
                if (count >= target) {
                    count = target;
                    clearInterval(timer);
                }
                clientsCounter.textContent = `+${count}`;
            }, 50);
        }

        // Toast "X personnes regardent ce produit"
        const productCards = document.querySelectorAll('.product-card');
        const toastElement = document.getElementById('product-view-toast');
        let toastTimeout;

        productCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                const viewers = Math.floor(Math.random() * 20) + 1; // Nombre aléatoire de spectateurs
                toastElement.textContent = `${viewers} personne${viewers > 1 ? 's' : ''} regardent ce produit`;
                toastElement.classList.add('show');

                clearTimeout(toastTimeout); // Efface le timeout précédent si existant
                toastTimeout = setTimeout(() => {
                    toastElement.classList.remove('show');
                }, 3000); // Le toast disparaît après 3 secondes
            });
            // Optionnel: cacher le toast quand la souris quitte la carte rapidement
            // card.addEventListener('mouseleave', () => {
            //    clearTimeout(toastTimeout);
            //    toastElement.classList.remove('show');
            // });
        });

        // Fallback pour JavaScript désactivé (contenu déjà visible via HTML/CSS)
        // Le JS améliore l'expérience, mais le site reste fonctionnel.
        // Par exemple, le carrousel sera scrollable manuellement.

        // Optimisation : lazy loading des images (à ajouter si beaucoup d'images)
        // document.addEventListener("DOMContentLoaded", function() {
        //   var lazyImages = [].slice.call(document.querySelectorAll("img.lazy"));
        //   if ("IntersectionObserver" in window) {
        //     let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
        //       entries.forEach(function(entry) {
        //         if (entry.isIntersecting) {
        //           let lazyImage = entry.target;
        //           lazyImage.src = lazyImage.dataset.src;
        //           lazyImage.classList.remove("lazy");
        //           lazyImageObserver.unobserve(lazyImage);
        //         }
        //       });
        //     });
        //     lazyImages.forEach(function(lazyImage) {
        //       lazyImageObserver.observe(lazyImage);
        //     });
        //   } else {
        //     // Fallback pour les navigateurs sans IntersectionObserver
        //   }
        // });

        // Barre de recherche avec suggestions (nécessite une API ou des données locales)
        const searchInput = document.querySelector('[data-testid="search-bar"]');
        const suggestionsContainer = document.getElementById('search-suggestions');
        if (searchInput && suggestionsContainer) {
            // Exemple de données de suggestion (à remplacer par une vraie source)
            const allProducts = [
                @if(isset($articles) && $articles->count() > 0)
                    @foreach($articles as $article) "{{ Str::limit(addslashes($article->title), 50) }}", @endforeach
                @endif
                @if(isset($categories) && $categories->count() > 0)
                    @foreach($categories as $category) "{{ Str::limit(addslashes($category->name), 50) }}", @endforeach
                @endif
            ].filter(Boolean); // Filter out potential empty strings if collections are empty

            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                if (query.length < 2) {
                    suggestionsContainer.innerHTML = '';
                    suggestionsContainer.classList.add('hidden');
                    return;
                }

                const filteredSuggestions = allProducts.filter(item => item.toLowerCase().includes(query)).slice(0, 5);

                if (filteredSuggestions.length > 0) {
                    suggestionsContainer.innerHTML = filteredSuggestions.map(s =>
                        `<a href="#" class="block px-4 py-2 hover:bg-gray-100">${s}</a>`
                    ).join('');
                    suggestionsContainer.classList.remove('hidden');
                } else {
                    suggestionsContainer.innerHTML = '';
                    suggestionsContainer.classList.add('hidden');
                }
            });

            // Cacher les suggestions si on clique ailleurs
            document.addEventListener('click', function(event) {
                if (!searchInput.contains(event.target) && !suggestionsContainer.contains(event.target)) {
                    suggestionsContainer.classList.add('hidden');
                }
            });
        }

        // Gestionnaire de clic pour les boutons "Ajouter au panier"
        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const productCard = this.closest('.product-card');
                const productId = productCard.dataset.productId;
                const url = `/cart/add/${productId}`; // Attention: Utiliser la génération de route Laravel serait mieux si possible ici.
                                                    // Mais pour un script simple, une URL directe peut suffire.
                                                    // Pour {{ route('cart.add', $article) }} il faudrait passer l'ID.

                // Désactiver le bouton pour éviter les clics multiples
                this.disabled = true;
                this.textContent = 'Ajout...';

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}', // Fallback pour CSRF
                        'Accept': 'application/json', // S'assurer que le serveur sait qu'on attend du JSON
                        'X-Requested-With': 'XMLHttpRequest' // Laravel utilise ça pour $request->expectsJson()
                    },
                    body: JSON.stringify({ quantity: 1 }) // Envoyer la quantité si besoin, sinon le contrôleur prend 1 par défaut
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; }); // Gérer les erreurs HTTP comme du JSON
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Mettre à jour le compteur du panier dans le header
                        const cartCountElement = document.querySelector('[data-testid="cart-count"]');
                        if (cartCountElement && data.cartTotalItems !== undefined) {
                            cartCountElement.textContent = data.cartTotalItems;
                        }
                        // Afficher un toast de succès
                        toastElement.textContent = data.success;
                        toastElement.classList.add('show');
                        setTimeout(() => toastElement.classList.remove('show'), 3000);
                    } else if (data.error) {
                         // Afficher un toast d'erreur
                        toastElement.textContent = data.error;
                        toastElement.style.backgroundColor = 'red'; // Temporairement pour distinguer l'erreur
                        toastElement.classList.add('show');
                        setTimeout(() => {
                            toastElement.classList.remove('show');
                            toastElement.style.backgroundColor = 'rgba(0,0,0,0.75)'; // Rétablir la couleur
                        }, 4000);
                    }
                })
                .catch(error => {
                    console.error('Error adding to cart:', error);
                    let errorMessage = "Une erreur s'est produite.";
                    if (error && error.error) { // Si l'erreur vient du JSON du serveur
                        errorMessage = error.error;
                    } else if (error && error.message) { // Erreur réseau ou autre
                        errorMessage = error.message;
                    }
                     // Afficher un toast d'erreur générique
                    toastElement.textContent = errorMessage;
                    toastElement.style.backgroundColor = 'red';
                    toastElement.classList.add('show');
                    setTimeout(() => {
                        toastElement.classList.remove('show');
                        toastElement.style.backgroundColor = 'rgba(0,0,0,0.75)';
                    }, 4000);
                })
                .finally(() => {
                    // Réactiver le bouton
                    this.disabled = false;
                    this.textContent = 'Ajouter au panier';
                });
            });
        });

        // Placeholder pour les filtres de produits (nécessite une logique JS plus avancée)
        const sortFilter = document.querySelector('[data-testid="filter-sort"]');
        const categoryFilter = document.querySelector('[data-testid="filter-category"]');

        if (sortFilter) {
            sortFilter.addEventListener('change', function() {
                alert(`Filtre de tri changé à: ${this.value}. Logique de filtrage à implémenter.`);
                // Recharger la page avec les paramètres de filtre ou utiliser AJAX
            });
        }

        if (categoryFilter) {
            categoryFilter.addEventListener('change', function() {
                alert(`Filtre de catégorie changé à: ${this.value}. Logique de filtrage à implémenter.`);
                // Recharger la page avec les paramètres de filtre ou utiliser AJAX
            });
        }

        console.log("Scripts JS chargés et optimisés. Logique de base pour boutons ajoutée.");
        // Taille totale du script estimée : < 10KB (hors Tailwind)
    </script>

</body>
</html>
