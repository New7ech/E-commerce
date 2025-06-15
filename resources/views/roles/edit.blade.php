@extends('layouts.app')

@section('title', 'Modifier le Rôle')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Modifier le Rôle</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('roles.index') }}">Rôles</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Modifier : {{ $role->name }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Modifier le Rôle : {{ $role->name }}</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Nom du rôle <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Guard Name - Add if needed
                    <div class="form-group">
                        <label for="guard_name">Guard Name</label>
                        <input type="text" class="form-control @error('guard_name') is-invalid @enderror" id="guard_name" name="guard_name" value="{{ old('guard_name', $role->guard_name) }}">
                        @error('guard_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    --}}

                    <div class="form-group mt-3">
                        <label class="fw-bold d-block mb-2">Permissions</label>
                        <div class="row">
                             @if($permissions->isEmpty())
                                <p class="text-muted col-12">Aucune permission disponible. Veuillez d'abord créer des permissions.</p>
                            @else
                                @foreach ($permissions->groupBy(function($item) { return explode('-', $item->name)[0] ?: 'Divers'; }) as $module => $modulePermissions)
                                    <div class="col-md-4 mb-3">
                                        <h6 class="fw-medium">{{ ucfirst($module) }}</h6>
                                        @foreach ($modulePermissions as $permission)
                                            <div class="form-check">
                                                {{-- Check if old('permissions') exists (validation failed), otherwise use $role->permissions --}}
                                                @php
                                                    $rolePermissions = old('permissions') ?? $role->permissions->pluck('id')->toArray();
                                                @endphp
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission-{{ $permission->id }}" class="form-check-input @error('permissions') is-invalid @enderror"
                                                       {{ (is_array($rolePermissions) && in_array($permission->id, $rolePermissions)) ? 'checked' : '' }}>
                                                <label for="permission-{{ $permission->id }}" class="form-check-label">{{ $permission->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        @error('permissions') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="card-action mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Enregistrer les Modifications
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn btn-danger">
                            <i class="fa fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
