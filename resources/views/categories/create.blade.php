@extends('layouts.app')

@section('title', 'Ajouter une Catégorie')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Ajouter une Catégorie</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('categories.index') }}">Catégories</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Ajouter</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Ajouter une Nouvelle Catégorie</h4>
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

                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                        <a href="{{ route('categories.index') }}" class="btn btn-danger">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
