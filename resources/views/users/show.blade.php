@extends('layouts.app')

@section('title', 'Détails de l\'Utilisateur')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Détails de l'Utilisateur</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('users.index') }}">Utilisateurs</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">{{ $user->name }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Utilisateur : {{ $user->name }}</h4>
                    <div class="ms-auto">
                         <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-round btn-sm">
                            <i class="fa fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-primary btn-round btn-sm">
                            <i class="fa fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo de profil de {{ $user->name }}" class="img-fluid img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <img src="{{ asset('assets/img/kaiadmin/logocommerce.PNG') }}" alt="Avatar par défaut" class="img-fluid img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                             {{-- Assuming default-avatar.png is in public/assets/img/ --}}
                        @endif
                    </div>
                    <div class="col-md-9">
                        <dl class="row">
                            <dt class="col-sm-3">Nom complet</dt>
                            <dd class="col-sm-9">{{ $user->name }}</dd>

                            <dt class="col-sm-3">Email</dt>
                            <dd class="col-sm-9">{{ $user->email }}</dd>

                            <dt class="col-sm-3">Téléphone</dt>
                            <dd class="col-sm-9">{{ $user->phone ?: 'N/A' }}</dd>

                            <dt class="col-sm-3">Adresse</dt>
                            <dd class="col-sm-9">{{ $user->address ?: 'N/A' }}</dd>

                            <dt class="col-sm-3">Rôles</dt>
                            <dd class="col-sm-9">
                                @forelse($user->roles as $role)
                                    <span class="badge bg-secondary me-1">{{ $role->name }}</span>
                                @empty
                                    Aucun rôle assigné
                                @endforelse
                            </dd>

                            <dt class="col-sm-3">Date de création</dt>
                            <dd class="col-sm-9">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>

                            <dt class="col-sm-3">Dernière modification</dt>
                            <dd class="col-sm-9">{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
             {{-- Card Footer can be used for additional actions if needed later --}}
            {{-- <div class="card-footer text-center">
                <button type="button" class="btn btn-primary btn-round">Autres actions...</button>
            </div> --}}
        </div>
    </div>
</div>
@endsection
