@extends('layouts.app')

@section('title', 'Liste des Permissions')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Permissions</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Permissions</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash')
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Liste des Permissions</h4>
                    <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i> Créer une Permission
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="permissions-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nom de la Permission</th>
                                {{-- Guard Name column can be added if needed, original view did not have it --}}
                                {{-- <th>Guard</th> --}}
                                <th style="width: 10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($permissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                {{-- <td>{{ $permission->guard_name }}</td> --}}
                                <td>
                                    <div class="form-button-action">
                                        <a href="{{ route('permissions.edit', $permission->id) }}" data-bs-toggle="tooltip" title="Modifier" class="btn btn-link btn-warning btn-lg">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette permission ?');">
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
                                <td colspan="2" class="text-center">Aucune permission trouvée.</td> {{-- Adjusted colspan if guard is not shown --}}
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
        $('#permissions-table').DataTable({
             // "order": [[0, "asc"]] // Example: order by name asc
        });
    });
</script>
@endpush
