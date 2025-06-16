@extends('layouts.app')

@section('title', 'Nos Produits')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Catalogue des Produits</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li> {{-- Assuming home is general public home --}}
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Produits</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        {{-- Search and Filter Form --}}
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Filtrer les Produits</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('products.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Rechercher un produit</label>
                            <input type="text" name="search" id="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Nom du produit...">
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="form-label">Catégorie</label>
                            <select name="category" id="category" class="form-select form-select-sm">
                                <option value="">Toutes les catégories</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (isset($category) && $category == $cat->id) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="price_min" class="form-label">Prix Min</label>
                            <input type="number" name="price_min" id="price_min" value="{{ $price_min ?? '' }}" class="form-control form-control-sm" placeholder="Min">
                        </div>
                        <div class="col-md-2">
                            <label for="price_max" class="form-label">Prix Max</label>
                            <input type="number" name="price_max" id="price_max" value="{{ $price_max ?? '' }}" class="form-control form-control-sm" placeholder="Max">
                        </div>
                        <div class="col-md-3"> {{-- Adjusted for layout --}}
                            <label for="sort_by" class="form-label">Trier par</label>
                            <select name="sort_by" id="sort_by" class="form-select form-select-sm">
                                <option value="">Par défaut</option>
                                <option value="price_asc" {{ (isset($sort_by) && $sort_by == 'price_asc') ? 'selected' : '' }}>Prix (Croissant)</option>
                                <option value="price_desc" {{ (isset($sort_by) && $sort_by == 'price_desc') ? 'selected' : '' }}>Prix (Décroissant)</option>
                                <option value="name_asc" {{ (isset($sort_by) && $sort_by == 'name_asc') ? 'selected' : '' }}>Nom (A-Z)</option>
                                <option value="name_desc" {{ (isset($sort_by) && $sort_by == 'name_desc') ? 'selected' : '' }}>Nom (Z-A)</option>
                                <option value="created_at_desc" {{ (isset($sort_by) && $sort_by == 'created_at_desc') ? 'selected' : '' }}>Plus récents</option>
                            </select>
                        </div>
                        <div class="col-md-2 align-self-end"> {{-- Ensure button aligns with inputs --}}
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fa fa-filter"></i> Filtrer
                            </button>
                        </div>
                    </div>
                    <div class="row g-3 mt-1"> {{-- New row for filter button if needed, or adjust above columns --}}
                        {{-- If the button is too cramped, it could be moved to its own row or a wider column --}}
                        {{-- For now, adjusted column widths above to make space --}}
                    </div>
                </form>
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title">Nos Produits</h4>
            </div>
            <div class="card-body">
                @if ($articles->count() > 0)
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                        @foreach ($articles as $article)
                            <div class="col">
                                <div class="card card-product h-100">
                                    @if ($article->image_path)
                                        <div class="card-header product-img p-0"> {{-- p-0 to remove padding if image is meant to fill header --}}
                                            <a href="{{ route('products.show', $article->id) }}">
                                            <img src="{{ Storage::url($article->image_path) }}" alt="{{ $article->name }}" class="img-fluid rounded-top" style="height: 200px; width:100%; object-fit: cover;">
                                            </a>
                                        </div>
                                    @else
                                        <div class="card-header product-img p-0">
                                            <a href="{{ route('products.show', $article->id) }}">
                                            <img src="{{ asset('assets/img/placeholder-product.jpg') }}" alt="Placeholder" class="img-fluid rounded-top" style="height: 200px; width:100%; object-fit: cover;">
                                            </a>
                                        </div>
                                    @endif
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title fw-bold"><a href="{{ route('products.show', $article->id) }}" class="text-dark text-decoration-none stretched-link">{{ $article->name }}</a></h5>
                                        <p class="card-text text-muted small">{{ $article->categorie->name ?? 'N/A' }}</p>
                                        <p class="card-text flex-grow-1">{{ Str::limit($article->description, 70) }}</p>
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="price fw-bold fs-5 mb-0 text-primary">{{ number_format($article->prix, 0, ',', ' ') }} FCFA</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                @auth
                                                    @if (in_array($article->id, $userWishlistArticleIds))
                                                        <form action="{{ route('wishlist.remove', $article->id) }}" method="POST" class="me-1 flex-grow-1">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-xs w-100" title="Retirer de la liste de souhaits">
                                                                <i class="fa fa-heart-broken"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('wishlist.add', $article->id) }}" method="POST" class="me-1 flex-grow-1">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-primary btn-xs w-100" title="Ajouter à la liste de souhaits">
                                                                <i class="fa fa-heart"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endauth
                                                <form action="{{ route('cart.add', $article->id) }}" method="POST" class="flex-grow-1 @auth ms-1 @endauth">
                                                    @csrf
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn btn-primary btn-sm w-100 @if($article->quantite <= 0) btn-outline-danger disabled @endif" {{ $article->quantite <= 0 ? 'disabled' : '' }}>
                                                        <i class="fa fa-cart-plus"></i>
                                                        @if($article->quantite <= 0) En rupture @else Panier @endif
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $articles->appends(request()->query())->links() }}
                    </div>
                @else
                    <p class="text-center py-5">Aucun produit trouvé correspondant à vos critères.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-product {
        transition: transform .2s ease-out, box-shadow .2s ease-out;
    }
    .card-product:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .card-product .product-img img {
        border-bottom: 1px solid #eee;
    }
</style>
@endpush
