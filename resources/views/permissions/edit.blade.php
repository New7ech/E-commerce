@extends('layouts.app')

@section('title', 'Modifier la Permission')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Modifier la Permission</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('permissions.index') }}">Permissions</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Modifier : {{ $permission->name }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Modifier la Permission : {{ $permission->name }}</h4>
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

                <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Nom de la permission <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $permission->name) }}" required placeholder="ex: articles-creer">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <small class="form-text text-muted">Utilisez un format comme `entite-action` (par exemple, `articles-lire`, `utilisateurs-modifier`).</small>
                        @enderror
                    </div>

                    {{-- As per file reading, guard_name is not in the original form. If needed, add:
                    <div class="form-group">
                        <label for="guard_name">Guard Name</label>
                        <input type="text" class="form-control @error('guard_name') is-invalid @enderror" id="guard_name" name="guard_name" value="{{ old('guard_name', $permission->guard_name) }}">
                        @error('guard_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                         <small class="form-text text-muted">Usually 'web' or 'api'.</small>
                    </div>
                    --}}

                    <div class="card-action mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Enregistrer les Modifications
                        </button>
                        <a href="{{ route('permissions.index') }}" class="btn btn-danger">
                            <i class="fa fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
