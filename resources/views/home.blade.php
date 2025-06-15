@extends('layouts.app')

@section('title', 'Accueil Public')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Accueil</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('home') }}"><i class="icon-home"></i></a></li>
        {{-- Add other breadcrumbs if this page is nested, e.g., under a public section --}}
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body text-center"> {{-- Added text-center for the welcome message --}}
                <h1 class="display-4 fw-bold text-primary my-4">Bienvenue dans Notre Boutique!</h1>
                <p class="lead text-muted mb-4">
                    Découvrez nos derniers produits et offres exceptionnelles.
                </p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg btn-round">
                    <i class="fa fa-shopping-bag me-2"></i> Parcourir les Produits
                </a>
            </div>
        </div>

        {{-- You can add other sections here like featured products, categories, etc. --}}
        {{-- For example:
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Produits Populaires</h4></div>
                    <div class="card-body">
                        {{-- Placeholder for popular products listing --}}
                        <p>Section des produits populaires à venir...</p>
                    </div>
                </div>
            </div>
        </div>
        --}}
    </div>
</div>
@endsection
