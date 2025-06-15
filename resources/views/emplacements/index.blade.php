@extends('layouts.app')

@section('title', 'Liste des Emplacements')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Emplacements</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('welcome') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Emplacements</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash')
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Liste des Emplacements</h4>
                    <a href="{{ route('emplacements.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i> Ajouter un Emplacement
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="emplacements-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th style="width: 10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($emplacements as $emplacement)
                            <tr>
                                <td>{{ $emplacement->name }}</td>
                                <td>{{ Str::limit($emplacement->description, 70) }}</td>
                                <td>
                                    <div class="form-button-action">
                                        <a href="{{ route('emplacements.show', $emplacement->id) }}" data-bs-toggle="tooltip" title="Voir" class="btn btn-link btn-primary btn-lg">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('emplacements.edit', $emplacement->id) }}" data-bs-toggle="tooltip" title="Modifier" class="btn btn-link btn-warning btn-lg">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('emplacements.destroy', $emplacement->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet emplacement ?');">
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
                                <td colspan="3" class="text-center">Aucun emplacement trouvé.</td>
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
        $('#emplacements-table').DataTable({
            // Add any specific DataTable options here if needed
        });
    });
</script>
@endpush
