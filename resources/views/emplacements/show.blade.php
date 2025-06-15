@extends('layouts.app')

@section('title', 'Détails de l\'Emplacement')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Détails de l'Emplacement</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('welcome') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('emplacements.index') }}">Emplacements</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">{{ $emplacement->name }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Emplacement : {{ $emplacement->name }}</h4>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Nom</dt>
                    <dd class="col-sm-9">{{ $emplacement->name }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $emplacement->description ?: 'N/A' }}</dd>

                    <dt class="col-sm-3">Date de création</dt>
                    <dd class="col-sm-9">{{ $emplacement->created_at ? $emplacement->created_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>

                    <dt class="col-sm-3">Dernière modification</dt>
                    <dd class="col-sm-9">{{ $emplacement->updated_at ? $emplacement->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>
                </dl>
            </div>
            <div class="card-footer">
                <a href="{{ route('emplacements.index') }}" class="btn btn-primary">Retour à la liste</a>
                <a href="{{ route('emplacements.edit', $emplacement->id) }}" class="btn btn-warning">Modifier</a>
            </div>
        </div>
    </div>
</div>
@endsection
