@extends('layouts.app')

@section('title', $article->name)

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">{{ $article->name }}</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('home') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('products.index') }}">Produits</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">{{ $article->name }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Détails du Produit</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        @if ($article->image_path && Storage::disk('public')->exists($article->image_path))
                            <img src="{{ Storage::url($article->image_path) }}" alt="{{ $article->name }}" class="img-fluid rounded shadow-sm" style="max-height: 500px; width: 100%; object-fit: contain;">
                        @else
                             <img src="{{ asset('assets/img/placeholder-product.jpg') }}" alt="Placeholder" class="img-fluid rounded shadow-sm" style="max-height: 500px; width: 100%; object-fit: contain;">
                        @endif
                        {{-- TODO: Consider adding a gallery for multiple images if applicable --}}
                    </div>
                    <div class="col-md-6">
                        <h2 class="fw-bold mb-2">{{ $article->name }}</h2>

                        <p class="text-muted mb-2">
                            Catégorie: <a href="{{ route('products.index', ['category' => $article->categorie->id ?? '']) }}" class="text-primary">{{ $article->categorie->name ?? 'N/A' }}</a>
                        </p>

                        <h3 class="fw-bold my-3 text-primary">{{ number_format($article->prix, 0, ',', ' ') }} FCFA</h3>

                        @if($article->quantite > 0) {{-- Using quantite --}}
                            <p class="text-success fw-semibold mb-2"><i class="fa fa-check-circle"></i> En Stock ({{ $article->quantite }} disponible(s))</p>
                        @else
                            <p class="text-danger fw-semibold mb-2"><i class="fa fa-times-circle"></i> En rupture de stock</p>
                        @endif

                        <div class="mt-3">
                            <h5 class="fw-semibold">Description</h5>
                            <p class="text-body-secondary" style="white-space: pre-wrap;">{{ $article->description ?? 'Aucune description disponible.' }}</p>
                        </div>

                        <div class="mt-3">
                            <h5 class="fw-semibold">Détails Additionnels</h5>
                            <ul class="list-unstyled text-muted">
                                <li><strong>Fournisseur:</strong> {{ $article->fournisseur->name ?? 'N/A' }}</li>
                                <li><strong>Emplacement:</strong> {{ $article->emplacement->name ?? 'N/A' }}</li>
                                {{-- Add other relevant details from the Article model as needed --}}
                            </ul>
                        </div>

                        <form action="{{ route('cart.add', $article->id) }}" method="POST" class="mt-4">
                            @csrf
                            <div class="row align-items-end g-2">
                                <div class="col-auto">
                                    <label for="quantity" class="form-label">Quantité:</label>
                                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $article->quantite > 0 ? $article->quantite : 1 }}" class="form-control form-control-sm @if($article->quantite <= 0) is-invalid @endif" style="width: 80px;" {{ $article->quantite <= 0 ? 'disabled' : '' }}>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary btn-lg btn-round w-100" {{ $article->quantite <= 0 ? 'disabled' : '' }}>
                                        <i class="fa fa-cart-plus me-2"></i>Ajouter au panier
                                    </button>
                                </div>
                            </div>
                             @if($article->quantite <= 0)
                                <small class="text-danger mt-1 d-block">Ce produit est actuellement en rupture de stock.</small>
                            @endif
                        </form>

                        {{-- Wishlist Button --}}
                        @auth
                        <div class="mt-3">
                            @if ($isInWishlist)
                                <form action="{{ route('wishlist.remove', $article->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fa fa-heart-broken"></i> Retirer de la liste de souhaits
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('wishlist.add', $article->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        <i class="fa fa-heart"></i> Ajouter à la liste de souhaits
                                    </button>
                                </form>
                            @endif
                        </div>
                        @endauth
                        {{-- End Wishlist Button --}}

                    </div>
                </div>

                <div class="mt-4 pt-4 border-top">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left"></i> Retour aux Produits
                    </a>
                </div>
            </div>
        </div>

        {{-- Related Products Section --}}
        @if ($relatedArticles && $relatedArticles->isNotEmpty())
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title">Vous pourriez aussi aimer</h4>
            </div>
            <div class="card-body">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                    @foreach ($relatedArticles as $relatedArticle)
                        <div class="col">
                            <div class="card card-product h-100">
                                <a href="{{ route('products.show', $relatedArticle->id) }}">
                                    @if ($relatedArticle->image_path && Storage::disk('public')->exists($relatedArticle->image_path))
                                        <img src="{{ Storage::url($relatedArticle->image_path) }}" alt="{{ $relatedArticle->name }}" class="img-fluid rounded-top" style="height: 150px; width:100%; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('assets/img/placeholder-product.jpg') }}" alt="Placeholder" class="img-fluid rounded-top" style="height: 150px; width:100%; object-fit: cover;">
                                    @endif
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold small">
                                        <a href="{{ route('products.show', $relatedArticle->id) }}" class="text-dark text-decoration-none stretched-link">{{ Str::limit($relatedArticle->name, 50) }}</a>
                                    </h5>
                                    <div class="mt-auto">
                                        <p class="price fw-bold text-primary mb-0">{{ number_format($relatedArticle->prix, 0, ',', ' ') }} FCFA</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        {{-- End Related Products Section --}}

    </div>
</div>
@endsection

@push('styles')
<style>
    /* Add any page-specific styles here */
</style>
@endpush
