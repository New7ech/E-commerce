@extends('layouts.app')

@section('title', 'Détails de l\'Article')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Détails de l'Article</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('welcome') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('articles.index') }}">Articles</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">{{ $article->name }}</li> {{-- Using name as per previous observations --}}
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Article : {{ $article->name }}</h4> {{-- Using name --}}
            </div>
            <div class="card-body">
                @if ($article->image_path && Storage::disk('public')->exists($article->image_path))
                    <div class="mb-3 text-center">
                        <img src="{{ asset('storage/' . $article->image_path) }}" alt="Image de l'article {{ $article->name }}" class="img-fluid img-thumbnail" style="max-height: 300px; border-radius: 5px;">
                    </div>
                @else
                    <div class="mb-3 text-center">
                        <p>Aucune image disponible pour cet article.</p>
                    </div>
                @endif

                <dl class="row">
                    <dt class="col-sm-3">Nom</dt>
                    <dd class="col-sm-9">{{ $article->name }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $article->description ?: 'N/A' }}</dd>

                    <dt class="col-sm-3">Prix</dt>
                    <dd class="col-sm-9">{{ number_format($article->prix, 2, ',', ' ') }} FCFA</dd> {{-- Using prix --}}

                    <dt class="col-sm-3">Quantité en Stock</dt>
                    <dd class="col-sm-9">{{ $article->quantite }}</dd> {{-- Using quantite --}}

                    <dt class="col-sm-3">Catégorie</dt>
                    <dd class="col-sm-9">{{ $article->categorie->name ?? 'N/A' }}</dd> {{-- Assuming categorie->name --}}

                    <dt class="col-sm-3">Fournisseur</dt>
                    <dd class="col-sm-9">{{ $article->fournisseur->name ?? 'N/A' }}</dd> {{-- Assuming fournisseur->name --}}

                    <dt class="col-sm-3">Emplacement</dt>
                    <dd class="col-sm-9">{{ $article->emplacement->name ?? 'N/A' }}</dd> {{-- Assuming emplacement->name --}}

                    <dt class="col-sm-3">Créé par</dt>
                    <dd class="col-sm-9">{{ $article->user->name ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Date de création</dt>
                    <dd class="col-sm-9">{{ $article->created_at ? $article->created_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>

                    <dt class="col-sm-3">Dernière modification</dt>
                    <dd class="col-sm-9">{{ $article->updated_at ? $article->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>
                </dl>
            </div>
            <div class="card-footer">
                <a href="{{ route('articles.index') }}" class="btn btn-primary">Retour à la liste</a>
                <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-warning">Modifier</a>
            </div>
        </div>
    </div>
</div>
@endsection
