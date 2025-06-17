@extends('layouts.app')

@section('title', 'Détails de la Facture')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Détails de la Facture</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('factures.index') }}">Factures</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Facture #{{ $facture->numero ?? $facture->id }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Facture #{{ $facture->numero ?? $facture->id }}</h4>
                    <div class="ms-auto">
                        <a href="{{ route('factures.pdf', $facture->id) }}" class="btn btn-info btn-round btn-sm" target="_blank">
                            <i class="fas fa-file-pdf"></i> Télécharger PDF
                        </a>
                         <a href="{{ route('factures.edit', $facture->id) }}" class="btn btn-warning btn-round btn-sm">
                            <i class="fa fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('factures.index') }}" class="btn btn-primary btn-round btn-sm">
                            <i class="fa fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="fw-bold">Client:</h5>
                        <address>
                            <strong>{{ $facture->client_nom }} {{ $facture->client_prenom }}</strong><br>
                            @if($facture->client_adresse) {{ $facture->client_adresse }}<br>@endif
                            @if($facture->client_telephone) Téléphone: {{ $facture->client_telephone }}<br>@endif
                            @if($facture->client_email) Email: {{ $facture->client_email }}@endif
                        </address>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h5 class="fw-bold">Facture Info:</h5>
                        <p class="mb-0"><strong>Date de facturation:</strong> {{ $facture->date_facture ? \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') : 'N/A' }}</p>
                        <p class="mb-0"><strong>Statut:</strong>
                            <span class="badge {{ $facture->statut_paiement == 'payé' ? 'bg-success' : ($facture->statut_paiement == 'partiel' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($facture->statut_paiement) }}
                            </span>
                        </p>
                        @if($facture->date_paiement)
                        <p class="mb-0"><strong>Date de paiement:</strong> {{ \Carbon\Carbon::parse($facture->date_paiement)->format('d/m/Y') }}</p>
                        @endif
                        @if($facture->mode_paiement)
                        <p class="mb-0"><strong>Mode de paiement:</strong> {{ ucfirst($facture->mode_paiement) }}</p>
                        @endif
                    </div>
                </div>

                <hr>
                <h5 class="mt-4 mb-3 fw-bold">Articles</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">#</th>
                                <th class="fw-semibold">Article</th>
                                <th class="text-end fw-semibold">Quantité</th>
                                <th class="text-end fw-semibold">Prix Unitaire HT</th>
                                <th class="text-end fw-semibold">Montant HT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facture->articles as $index => $article)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $article->name }}</td>
                                <td class="text-end">{{ $article->pivot->quantite }}</td>
                                <td class="text-end">{{ number_format($article->pivot->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end">{{ number_format($article->pivot->quantite * $article->pivot->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="fw-bold">
                            <tr>
                                <td colspan="3"></td>
                                <td class="text-end">Montant HT Total:</td>
                                <td class="text-end">{{ number_format($facture->montant_ht, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td class="text-end">TVA ({{ $facture->tva ?? 18 }}%):</td>
                                <td class="text-end">{{ number_format($facture->montant_ht * (($facture->tva ?? 18) / 100), 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td class="text-end fs-5">Montant TTC:</td>
                                <td class="text-end fs-5">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                 @if($facture->notes)
                    <hr class="mt-5">
                    <div class="mt-3">
                        <h5 class="fw-bold">Notes :</h5>
                        <p>{{ $facture->notes }}</p>
                    </div>
                @endif
            </div>
            {{-- Card Footer can be used for additional actions if needed later --}}
            {{-- <div class="card-footer text-center">
                <button type="button" class="btn btn-primary btn-round">Autres actions...</button>
            </div> --}}
        </div>
    </div>
</div>
@endsection
