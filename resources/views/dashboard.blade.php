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

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <p>{{ __("You're logged in!") }}</p>
                <p>Bienvenue sur votre tableau de bord.</p>
                {{-- You can add more dashboard-specific content here if needed,
                     or direct users to the main welcome/statistics page if that serves as the primary dashboard.
                --}}
                <p>Pour les statistiques détaillées et la gestion, veuillez consulter la <a href="#">page d'accueil principale</a> ou les sections dédiées.</p>
            </div>
        </div>
    </div>
</div>
@endsection
