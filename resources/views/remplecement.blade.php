@extends('layouts.app')

@section('title', 'Page de Remplacement')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Page de Remplacement</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Remplacement</li> {{-- Or a more appropriate breadcrumb --}}
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Contenu de Remplacement</h4>
            </div>
            <div class="card-body">
                <p>Cette page est en cours de construction ou sert de remplacement temporaire.</p>
                <p>Veuillez vérifier ultérieurement pour le contenu mis à jour.</p>
                {{-- Add any specific instructions or links if necessary --}}
                <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">Retour à la page précédente</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Add any specific JS for this page if needed --}}
@endpush
