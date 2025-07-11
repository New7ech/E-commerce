@extends('layouts.app')

@section('title', 'Liste des Articles')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Articles</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
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
                    <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-round ms-auto">
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
                            @forelse ($articles as $article)
                            <tr>
                                <td>{{ $article->name }}</td>
                                <td>{{ $article->categorie->name ?? 'N/A' }}</td>
                                <td>{{ number_format($article->prix, 2, ',', ' ') }} FCFA</td>
                                <td>{{ $article->quantite }}</td>
                                <td>
                                    <div class="form-button-action">
                                        <a href="{{ route('admin.articles.edit', $article->id) }}" data-bs-toggle="tooltip" title="Modifier" class="btn btn-link btn-primary btn-lg">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
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
        $('#add-row').DataTable({}); // Basic DataTable initialization
    });
</script>
@endpush
