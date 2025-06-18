@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Tableau de Bord</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Tableau de Bord</li>
    </ul>
</div>

{{-- Dashboard Essentials (Counts) --}}
<div class="row">
    <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-box-open"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Produits</p>
                            <h4 class="card-title">{{ $productCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Catégories</p>
                            <h4 class="card-title">{{ $categoryCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Utilisateurs</p>
                            <h4 class="card-title">{{ $userCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Promotional Banners Section --}}
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Promotions Spéciales</h5>
                <img src="https://via.placeholder.com/1200x300.png?text=Bannière+Promotionnelle" class="img-fluid rounded" alt="Bannière Promotionnelle">
                <p class="mt-2">Découvrez nos offres exclusives cette semaine !</p>
            </div>
        </div>
    </div>
</div>

{{-- Produits Récemment Mis à Jour Section --}}
<div class="row mt-4">
    <div class="col-md-12">
        <h3 class="fw-bold mb-3">Produits Récemment Mis à Jour</h3>
    </div>
    @forelse($recentlyUpdatedProducts as $article)
        <div class="col-md-3 mb-4">
            <div class="card">
                <img src="{{ $article->image_path ? asset('storage/' . $article->image_path) : 'https://via.placeholder.com/300x200.png?text=Image+Produit' }}" class="card-img-top" alt="{{ $article->name }}" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">{{ $article->name }}</h5>
                    <p class="card-text">{{ Str::limit($article->description, 50) }}</p>
                    <p class="card-text"><strong>Prix: {{ number_format($article->prix, 2, ',', ' ') }} FCFA</strong></p>
                    <a href="{{ route('products.show', $article->id) }}" class="btn btn-primary btn-sm">Voir Détails</a>
                </div>
                @if($article->categorie)
                <div class="card-footer text-muted">
                    <small>Catégorie: {{ $article->categorie->name }}</small>
                </div>
                @endif
            </div>
        </div>
    @empty
        <div class="col-md-12">
            <p>Aucun produit récemment mis à jour.</p>
        </div>
    @endforelse
</div>

{{-- Nouveautés Section --}}
<div class="row mt-4">
    <div class="col-md-12">
        <h3 class="fw-bold mb-3">Nouveautés</h3>
    </div>
    @forelse($newArrivals as $article)
        <div class="col-md-3 mb-4">
            <div class="card">
                <img src="{{ $article->image_path ? asset('storage/' . $article->image_path) : 'https://via.placeholder.com/300x200.png?text=Nouveau+Produit' }}" class="card-img-top" alt="{{ $article->name }}" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">{{ $article->name }}</h5>
                    <p class="card-text">{{ Str::limit($article->description, 50) }}</p>
                    <p class="card-text"><strong>Prix: {{ number_format($article->prix, 2, ',', ' ') }} FCFA</strong></p>
                    <a href="{{ route('products.show', $article->id) }}" class="btn btn-primary btn-sm">Voir Détails</a>
                </div>
                @if($article->categorie)
                <div class="card-footer text-muted">
                    <small>Catégorie: {{ $article->categorie->name }}</small>
                </div>
                @endif
            </div>
        </div>
    @empty
        <div class="col-md-12">
            <p>Aucune nouveauté pour le moment.</p>
        </div>
    @endforelse
</div>

@endsection
