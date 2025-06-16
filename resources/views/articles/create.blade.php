@extends('layouts.app')

@section('title', 'Ajouter un Article')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Ajouter un Article</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('articles.index') }}">Articles</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Ajouter</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Formulaire d'Ajout d'Article</h4>
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

                <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nom de l'article</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="prix">Prix (FCFA)</label>
                                <input type="number" step="any" class="form-control @error('prix') is-invalid @enderror" id="prix" name="prix" value="{{ old('prix') }}" required>
                                @error('prix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantite">Quantité en Stock</label>
                                <input type="number" class="form-control @error('quantite') is-invalid @enderror" id="quantite" name="quantite" value="{{ old('quantite', 0) }}" required>
                                @error('quantite') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="categorie_id">Catégorie</label>
                        <select class="form-control @error('categorie_id') is-invalid @enderror" id="categorie_id" name="categorie_id">
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $categorie) {{-- Assuming $categories is passed from controller --}}
                                <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>{{ $categorie->nom }}</option> {{-- Assuming 'nom' for categorie name --}}
                            @endforeach
                        </select>
                        @error('categorie_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="fournisseur_id">Fournisseur</label>
                        <select class="form-control @error('fournisseur_id') is-invalid @enderror" id="fournisseur_id" name="fournisseur_id">
                            <option value="">Sélectionner un fournisseur</option>
                            @foreach($fournisseurs as $fournisseur) {{-- Assuming $fournisseurs is passed from controller --}}
                                <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>{{ $fournisseur->nom }}</option> {{-- Assuming 'nom' for fournisseur name --}}
                            @endforeach
                        </select>
                        @error('fournisseur_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="emplacement_id">Emplacement</label>
                        <select class="form-control @error('emplacement_id') is-invalid @enderror" id="emplacement_id" name="emplacement_id">
                            <option value="">Sélectionner un emplacement</option>
                            @foreach($emplacements as $emplacement) {{-- Assuming $emplacements is passed from controller --}}
                                <option value="{{ $emplacement->id }}" {{ old('emplacement_id') == $emplacement->id ? 'selected' : '' }}>{{ $emplacement->nom }}</option> {{-- Assuming 'nom' for emplacement name --}}
                            @endforeach
                        </select>
                        @error('emplacement_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="image_path">Image de l'article</label>
                        <input type="file" class="form-control-file @error('image_path') is-invalid @enderror" id="image_path" name="image_path"> {{-- Consider using form-control for consistency if Kaiadmin styles form-control-file differently --}}
                        @error('image_path') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="card-action">
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                        <a href="{{ route('articles.index') }}" class="btn btn-danger">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
