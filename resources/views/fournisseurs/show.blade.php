@extends('layouts.app')

@section('title', 'Détails du Fournisseur')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Détails du Fournisseur</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('fournisseurs.index') }}">Fournisseurs</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">{{ $fournisseur->name }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Fournisseur : {{ $fournisseur->name }}</h4>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Nom du fournisseur</dt>
                    <dd class="col-sm-9">{{ $fournisseur->name }}</dd>

                    <dt class="col-sm-3">Nom de l'entreprise</dt>
                    <dd class="col-sm-9">{{ $fournisseur->nom_entreprise ?: 'N/A' }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $fournisseur->description ?: 'N/A' }}</dd>

                    <dt class="col-sm-3">Adresse</dt>
                    <dd class="col-sm-9">{{ $fournisseur->adresse ?: 'N/A' }}</dd>

                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9">{{ $fournisseur->email ?: 'N/A' }}</dd>

                    <dt class="col-sm-3">Téléphone</dt>
                    <dd class="col-sm-9">{{ $fournisseur->telephone ?: 'N/A' }}</dd>

                    <dt class="col-sm-3">Ville</dt>
                    <dd class="col-sm-9">{{ $fournisseur->ville ?: 'N/A' }}</dd>

                    <dt class="col-sm-3">Pays</dt>
                    <dd class="col-sm-9">{{ $fournisseur->pays ?: 'N/A' }}</dd>

                    <dt class="col-sm-3">Date de création</dt>
                    <dd class="col-sm-9">{{ $fournisseur->created_at ? $fournisseur->created_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>

                    <dt class="col-sm-3">Dernière modification</dt>
                    <dd class="col-sm-9">{{ $fournisseur->updated_at ? $fournisseur->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>
                </dl>
            </div>
            <div class="card-footer">
                <a href="{{ route('fournisseurs.index') }}" class="btn btn-primary">Retour à la liste</a>
                <a href="{{ route('fournisseurs.edit', $fournisseur->id) }}" class="btn btn-warning">Modifier</a>
            </div>
        </div>
    </div>
</div>
@endsection
