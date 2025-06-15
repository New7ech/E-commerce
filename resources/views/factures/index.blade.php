@extends('layouts.app')

@section('title', 'Liste des Factures')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Factures</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('welcome') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Factures</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash')
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Liste des Factures</h4>
                    <a href="{{ route('factures.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i> Ajouter une Facture
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('factures.index') }}" method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher par numéro, client..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table id="factures-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Numéro</th>
                                <th>Client</th>
                                <th>Date Facture</th>
                                <th>Montant TTC</th>
                                <th>Statut</th>
                                <th style="width: 15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($factures as $facture)
                            <tr>
                                <td>{{ $facture->numero ?? $facture->id }}</td>
                                <td>{{ $facture->client_nom }} {{ $facture->client_prenom }}</td>
                                <td>{{ $facture->date_facture ? \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    <span class="badge {{ $facture->statut_paiement == 'payé' ? 'bg-success' : ($facture->statut_paiement == 'partiel' ? 'bg-warning' : 'bg-danger') }}">
                                        {{ ucfirst($facture->statut_paiement) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="form-button-action">
                                        <a href="{{ route('factures.show', $facture->id) }}" data-bs-toggle="tooltip" title="Voir" class="btn btn-link btn-primary btn-lg">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('factures.edit', $facture->id) }}" data-bs-toggle="tooltip" title="Modifier" class="btn btn-link btn-warning btn-lg">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="{{ route('factures.pdf', $facture->id) }}" data-bs-toggle="tooltip" title="Télécharger PDF" class="btn btn-link btn-info btn-lg" target="_blank">
                                            <i class="fa fa-file-pdf"></i>
                                        </a>
                                        <form action="{{ route('factures.destroy', $facture->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?');">
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
                                <td colspan="6" class="text-center">Aucune facture trouvée.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($factures->hasPages())
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $factures->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#factures-table').DataTable({
            "searching": false, // Disable DataTables search as we have a custom search form
            "paging": false, // Disable DataTables paging if using Laravel pagination
            "info": false // Disable DataTables info if using Laravel pagination
            // You can add other options like ordering, language, etc.
        });
    });
</script>
@endpush
