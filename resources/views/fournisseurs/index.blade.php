@extends('layouts.app')

@section('title', 'Liste des Fournisseurs')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Fournisseurs</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('welcome') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Fournisseurs</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash')
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Liste des Fournisseurs</h4>
                    <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i> Ajouter un Fournisseur
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="fournisseurs-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Entreprise</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Ville</th>
                                <th style="width: 10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($fournisseurs as $fournisseur)
                            <tr>
                                <td>{{ $fournisseur->name }}</td>
                                <td>{{ $fournisseur->nom_entreprise }}</td>
                                <td>{{ $fournisseur->email }}</td>
                                <td>{{ $fournisseur->telephone }}</td>
                                <td>{{ $fournisseur->ville }}</td>
                                <td>
                                    <div class="form-button-action">
                                        <a href="{{ route('fournisseurs.show', $fournisseur->id) }}" data-bs-toggle="tooltip" title="Voir" class="btn btn-link btn-primary btn-lg">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('fournisseurs.edit', $fournisseur->id) }}" data-bs-toggle="tooltip" title="Modifier" class="btn btn-link btn-warning btn-lg"> {{-- Changed to btn-warning for edit --}}
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('fournisseurs.destroy', $fournisseur->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" data-bs-toggle="tooltip" title="Supprimer" class="btn btn-link btn-danger">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucun fournisseur trouvé.</td> {{-- Adjusted colspan --}}
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#fournisseurs-table').DataTable({
            // Add any specific DataTable options here if needed
        });
    });
</script>
@endpush
