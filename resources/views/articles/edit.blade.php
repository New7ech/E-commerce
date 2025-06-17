@extends('layouts.app')

@section('title', 'Modifier l\'Article')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Modifier l'Article</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('admin.articles.index') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('admin.articles.index') }}">Articles</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Modifier : {{ $article->name }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Modifier l'Article : {{ $article->name }}</h4>
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

                <form action="{{ route('admin.articles.update', $article->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Nom de l'article</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $article->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $article->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="prix">Prix (FCFA)</label>
                                <input type="number" step="any" class="form-control @error('prix') is-invalid @enderror" id="prix" name="prix" value="{{ old('prix', $article->prix) }}" required>
                                @error('prix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantite">Quantité en stock</label>
                                <input type="number" class="form-control @error('quantite') is-invalid @enderror" id="quantite" name="quantite" value="{{ old('quantite', $article->quantite) }}" required>
                                @error('quantite') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Catégorie</label>
                        <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $categorie)
                                <option value="{{ $categorie->id }}" {{ old('category_id', $article->category_id) == $categorie->id ? 'selected' : '' }}>{{ $categorie->name }}</option> {{-- Assuming $categorie->name based on original files --}}
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="fournisseur_id">Fournisseur</label>
                        <select class="form-control @error('fournisseur_id') is-invalid @enderror" id="fournisseur_id" name="fournisseur_id">
                            <option value="">Sélectionner un fournisseur</option>
                            @foreach($fournisseurs as $fournisseur)
                                <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id', $article->fournisseur_id) == $fournisseur->id ? 'selected' : '' }}>{{ $fournisseur->name }}</option> {{-- Assuming $fournisseur->name --}}
                            @endforeach
                        </select>
                        @error('fournisseur_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="emplacement_id">Emplacement</label>
                        <select class="form-control @error('emplacement_id') is-invalid @enderror" id="emplacement_id" name="emplacement_id">
                            <option value="">Sélectionner un emplacement</option>
                            @foreach($emplacements as $emplacement)
                                <option value="{{ $emplacement->id }}" {{ old('emplacement_id', $article->emplacement_id) == $emplacement->id ? 'selected' : '' }}>{{ $emplacement->name }}</option> {{-- Assuming $emplacement->name --}}
                            @endforeach
                        </select>
                        @error('emplacement_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="image_path">Image de l'article</label>
                        <input type="file" class="form-control-file @error('image_path') is-invalid @enderror" id="image_path" name="image_path">
                        @error('image_path') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @if ($article->image_path)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $article->image_path) }}" alt="Image actuelle" style="max-height: 100px; border-radius: 5px;">
                            </div>
                        @endif
                    </div>

                    <div class="card-action">
                        <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-danger">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
