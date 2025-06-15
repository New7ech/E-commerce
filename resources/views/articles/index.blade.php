@extends('layouts.app')

@section('title', 'Liste des Articles')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Articles</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('welcome') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Articles</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash') {{-- Keep for now, style later if needed --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Liste des Articles</h4>
                    <a href="{{ route('articles.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i>
                        Ajouter un Article
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="add-row" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                                <th style="width: 10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($articles as $article)
                            <tr>
                                <td>{{ $article->nom }}</td>
                                <td>{{ $article->categorie->nom ?? 'N/A' }}</td> {{-- Assuming 'nom' for categorie name based on your example --}}
                                <td>{{ number_format($article->prix, 2, ',', ' ') }} FCFA</td>
                                <td>{{ $article->quantite_stock }}</td> {{-- Changed from quantite to quantite_stock --}}
                                <td>
                                    <div class="form-button-action">
                                        <a href="{{ route('articles.show', $article->id) }}" data-bs-toggle="tooltip" title="Voir" class="btn btn-link btn-primary btn-lg">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('articles.edit', $article->id) }}" data-bs-toggle="tooltip" title="Modifier" class="btn btn-link btn-primary btn-lg"> {{-- Changed btn-primary to btn-warning to match common practice for edit --}}
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('articles.destroy', $article->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
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
                                <td colspan="5" class="text-center">Aucun article trouvé.</td>
                            </tr>
                            @endforeach
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
        $('#add-row').DataTable({}); // Basic DataTable initialization
    });
</script>
@endpush
