@extends('layouts.app')

@section('title', 'Liste des Rôles')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Rôles</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Rôles</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash')
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Liste des Rôles</h4>
                    <a href="{{ route('roles.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i> Créer un Rôle
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="roles-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nom du Rôle</th>
                                <th>Permissions (Extrait)</th>
                                {{-- Guard Name column can be added if needed --}}
                                {{-- <th>Guard</th> --}}
                                <th style="width: 10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>
                                    @if($role->permissions->isNotEmpty())
                                        @foreach($role->permissions->take(5) as $permission)
                                            <span class="badge bg-secondary me-1">{{ $permission->name }}</span>
                                        @endforeach
                                        @if($role->permissions->count() > 5)
                                            <span class="badge bg-light text-dark">+ {{ $role->permissions->count() - 5 }} autres</span>
                                        @endif
                                    @else
                                        <span class="badge bg-light text-dark">Aucune permission</span>
                                    @endif
                                </td>
                                {{-- <td>{{ $role->guard_name }}</td> --}}
                                <td>
                                    <div class="form-button-action">
                                        <a href="{{ route('roles.edit', $role->id) }}" data-bs-toggle="tooltip" title="Modifier" class="btn btn-link btn-warning btn-lg">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?');">
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
                                <td colspan="3" class="text-center">Aucun rôle trouvé.</td> {{-- Adjust colspan if guard name is not shown --}}
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
        $('#roles-table').DataTable({
            // "order": [[0, "asc"]] // Example: order by name asc
        });
    });
</script>
@endpush
