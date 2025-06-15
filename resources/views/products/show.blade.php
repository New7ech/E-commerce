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
                    </div>
                </div>

                <div class="mt-4 pt-4 border-top">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left"></i> Retour aux Produits
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Add any page-specific styles here */
</style>
@endpush
