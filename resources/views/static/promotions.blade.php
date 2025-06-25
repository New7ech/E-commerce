@extends('layouts.app')

@section('title', 'Promotions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Nos Promotions Actuelles</h1>
    <div class="bg-white p-6 shadow rounded-lg">
        <p>Découvrez bientôt ici nos meilleures offres et promotions !</p>
        {{-- Ici, vous listeriez les produits en promotion --}}
        {{-- Exemple :
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-6">
            @if(isset($promo_articles) && $promo_articles->count() > 0)
                @foreach ($promo_articles as $article)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover-scale product-card" data-product-id="{{ $article->id }}">
                        <a href="{{ route('products.show', $article) }}">
                            <img src="{{ $article->image_url ?? 'https://via.placeholder.com/300x200?text=Produit' }}" alt="{{ $article->title }}" class="w-full h-48 object-cover">
                        </a>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-1 truncate" title="{{ $article->title }}">
                                <a href="{{ route('products.show', $article) }}">{{ $article->title }}</a>
                            </h3>
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
                            <button class="w-full bg-primary-orange text-white py-2 rounded-lg hover:bg-opacity-90 transition duration-300 add-to-cart-btn">
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="col-span-full text-center text-gray-500">Aucune promotion pour le moment.</p>
            @endif
        </div>
        --}}
    </div>
</div>
@endsection
