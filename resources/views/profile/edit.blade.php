@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Mon Profil</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li> {{-- Assuming dashboard is the welcome route or similar --}}
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Profil</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        {{-- Navigation for Profile Section --}}
        <div class="card">
            <div class="card-body">
                <nav class="nav nav-pills nav-fill">
                    <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                        Modifier le Profil
                    </a>
                    <a class="nav-link {{ request()->routeIs('profile.orders') ? 'active' : '' }}" href="{{ route('profile.orders') }}">
                        Historique des Commandes
                    </a>
                    {{-- Add other profile related links here if needed --}}
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Informations du Profil</h4>
            </div>
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Changer le Mot de Passe</h4>
            </div>
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Supprimer le Compte</h4>
            </div>
            <div class="card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
