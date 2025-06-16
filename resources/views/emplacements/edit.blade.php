@extends('layouts.app')

@section('title', 'Modifier l\'Emplacement')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Modifier l'Emplacement</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('emplacements.index') }}">Emplacements</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Modifier : {{ $emplacement->name }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Modifier l'Emplacement : {{ $emplacement->name }}</h4>
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

                <form action="{{ route('emplacements.update', $emplacement->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Nom de l'emplacement</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $emplacement->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $emplacement->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
                        <a href="{{ route('emplacements.index') }}" class="btn btn-danger">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
