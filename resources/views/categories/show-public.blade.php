@extends('layouts.app') {{-- Assurez-vous que ce layout existe et inclut Tailwind/styles nécessaires --}}

@section('title', "Catégorie : " . $category->name)

@section('content')
<div class="container mx-auto px-4 py-8">

    <nav class="text-sm mb-4" aria-label="Breadcrumb">
        <ol class="list-none p-0 inline-flex">
            <li class="flex items-center">
                <a href="{{ route('homepage') }}" class="text-primary-orange hover:underline">Accueil</a>
                <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
            </li>
            <li>
                <span class="text-gray-500">{{ $category->name }}</span>
            </li>
        </ol>
    </nav>

    <h1 class="text-3xl font-bold text-gray-800 mb-6" data-testid="category-title">
        {{ $category->name }}
    </h1>

    @if($category->description)
        <p class="text-gray-600 mb-6">{{ $category->description }}</p>
    @endif

    <!-- Section Produits -->
    <section data-testid="products-section">
        @if($articles->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($articles as $article)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover-scale product-card" data-product-id="{{ $article->id }}" data-testid="product-card-{{ $article->id }}">
                    <a href="{{ route('products.show', $article) }}">
                        <img src="{{ $article->image_url ?? 'https://via.placeholder.com/300x200?text=Produit' }}" alt="{{ $article->title }}" class="w-full h-48 object-cover">
                    </a>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1 truncate" title="{{ $article->title }}">
                            <a href="{{ route('products.show', $article) }}">{{ $article->title }}</a>
                        </h3>
                        {{-- <p class="text-sm text-gray-500 mb-2">{{ $article->category->name }}</p> --}} {{-- Pas besoin de répéter la catégorie ici --}}
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
                @endforeach
            </div>
            <!-- Pagination -->
            @if ($articles->hasPages())
            <div class="mt-8" data-testid="pagination-links">
                {{ $articles->links() }}
            </div>
            @endif
        @else
            <p class="text-center text-gray-500 text-lg">Aucun produit trouvé dans cette catégorie pour le moment.</p>
        @endif
    </section>
</div>

{{-- Inclure le script pour "add-to-cart-btn" si layout.app ne l'a pas globalement --}}
{{-- Ou mieux, déplacer le script dans un fichier JS séparé et l'importer --}}
<script>
    // Le script pour add-to-cart-btn et toast est déjà dans welcome.blade.php
    // S'il n'est pas global (via app.js), il faudrait le dupliquer ou le rendre global.
    // Pour l'instant, on suppose que l'utilisateur navigue et que le script de welcome n'est pas actif ici.
    // Solution rapide (mais pas idéale) : répéter le script nécessaire
    document.addEventListener('DOMContentLoaded', function() {
        const toastElement = document.getElementById('product-view-toast') || document.createElement('div'); // Créer si absent
        if (!document.getElementById('product-view-toast')) {
            toastElement.id = 'product-view-toast';
            toastElement.classList.add('toast'); // Assurez-vous que la classe .toast est définie globalement
            document.body.appendChild(toastElement);
        }


        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const productCard = this.closest('.product-card');
                const productId = productCard.dataset.productId;
                const url = `/cart/add/${productId}`;
                this.disabled = true;
                this.textContent = 'Ajout...';

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ quantity: 1 })
                })
                .then(response => {
                    if (!response.ok) { return response.json().then(err => { throw err; }); }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const cartCountElement = document.querySelector('[data-testid="cart-count"]'); // Peut être null si pas dans le layout global
                        if (cartCountElement && data.cartTotalItems !== undefined) {
                            cartCountElement.textContent = data.cartTotalItems;
                        }
                        toastElement.textContent = data.success;
                        toastElement.classList.add('show');
                        toastElement.style.backgroundColor = 'rgba(0,0,0,0.75)';
                        setTimeout(() => toastElement.classList.remove('show'), 3000);
                    } else if (data.error) {
                        toastElement.textContent = data.error;
                        toastElement.style.backgroundColor = 'red';
                        toastElement.classList.add('show');
                        setTimeout(() => {
                            toastElement.classList.remove('show');
                            toastElement.style.backgroundColor = 'rgba(0,0,0,0.75)';
                        }, 4000);
                    }
                })
                .catch(error => {
                    console.error('Error adding to cart:', error);
                    let errorMessage = "Une erreur s'est produite.";
                    if (error && error.error) { errorMessage = error.error; }
                    else if (error && error.message) { errorMessage = error.message; }
                    toastElement.textContent = errorMessage;
                    toastElement.style.backgroundColor = 'red';
                    toastElement.classList.add('show');
                    setTimeout(() => {
                        toastElement.classList.remove('show');
                        toastElement.style.backgroundColor = 'rgba(0,0,0,0.75)';
                    }, 4000);
                })
                .finally(() => {
                    this.disabled = false;
                    this.textContent = 'Ajouter au panier';
                });
            });
        });
    });
</script>
@endsection
