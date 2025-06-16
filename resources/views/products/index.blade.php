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
                        <div class="col-md-5">
                            <label for="search" class="form-label">Rechercher un produit</label>
                            <input type="text" name="search" id="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Nom du produit...">
                        </div>
                        <div class="col-md-5">
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
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fa fa-filter"></i> Filtrer
                            </button>
                        </div>
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
                                             <form action="{{ route('cart.add', $article->id) }}" method="POST" class="mt-2">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-primary btn-sm w-100 @if($article->quantite <= 0) btn-outline-danger disabled @endif" {{ $article->quantite <= 0 ? 'disabled' : '' }}>
                                                    <i class="fa fa-cart-plus"></i>
                                                    @if($article->quantite <= 0) En rupture @else Ajouter au panier @endif
                                                </button>
                                            </form>
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
