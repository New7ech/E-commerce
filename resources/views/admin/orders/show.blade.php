@extends('layouts.app') {{-- Changed from x-app-layout --}}

@section('title', 'Détails de la Commande #' . $order->id)

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Détails de la Commande</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('admin.orders.index') }}">Commandes</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Commande #{{ $order->id }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        @if (session('success'))
            <div class="alert alert-success mb-3" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger mb-3" role="alert">
                <ul class="list-unstyled mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Order Details and Items -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Commande #{{ $order->id }} -
                        <span class="badge
                            @if($order->status == 'delivered' || $order->status == 'shipped') bg-info
                            @elseif($order->status == 'processing') bg-primary
                            @elseif($order->status == 'pending_payment') bg-warning text-dark
                            @elseif($order->status == 'cancelled' || $order->status == 'refunded') bg-danger
                            @else bg-secondary @endif">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </h4>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted">Passée le : {{ $order->created_at->format('d/m/Y H:i') }}</p>

                <dl class="row mt-4">
                    <dt class="col-sm-4 fw-semibold">Client</dt>
                    <dd class="col-sm-8">
                        {{ $order->user->name ?? $order->shipping_name ?? 'N/A' }} <br>
                        {{ $order->user->email ?? '' }}
                    </dd>

                    <dt class="col-sm-4 fw-semibold">Montant Total</dt>
                    <dd class="col-sm-8">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</dd>

                    <dt class="col-sm-4 fw-semibold">Mode de Paiement</dt>
                    <dd class="col-sm-8">{{ $order->payment_method ?? 'N/A' }}</dd>

                    <dt class="col-sm-4 fw-semibold">Statut Paiement</dt>
                    <dd class="col-sm-8">
                        <span class="badge
                            @if($order->payment_status == 'paid') bg-success
                            @elseif($order->payment_status == 'pending') bg-warning text-dark
                            @else bg-danger @endif">
                            {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                        </span>
                    </dd>

                    <dt class="col-sm-4 fw-semibold">Adresse de Livraison</dt>
                    <dd class="col-sm-8">
                        {{ $order->shipping_name }}<br>
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_postal_code }}<br>
                        {{ $order->shipping_country }}
                    </dd>

                    @if($order->billing_name)
                    <dt class="col-sm-4 fw-semibold">Adresse de Facturation</dt>
                    <dd class="col-sm-8">
                        {{ $order->billing_name }}<br>
                        {{ $order->billing_address }}<br>
                        {{ $order->billing_city }}, {{ $order->billing_postal_code }}<br>
                        {{ $order->billing_country }}
                    </dd>
                    @endif
                </dl>

                <hr class="my-4">
                <h5 class="fw-bold mb-3">Articles Commandés</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Article</th>
                                <th class="text-end">Quantité</th>
                                <th class="text-end">Prix Unitaire</th>
                                <th class="text-end">Total Article</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('products.show', $item->article->id) }}" target="_blank">{{ $item->article->name }}</a>
                                    <br><small class="text-muted">SKU: {{ $item->article->id }}</small>
                                </td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->price, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end">{{ number_format($item->price * $item->quantity, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions and Status Update -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Mettre à Jour le Statut</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="status" class="form-label">Statut de la Commande</label>
                        <select id="status" name="status" class="form-select">
                            <option value="pending_payment" {{ $order->status === 'pending_payment' ? 'selected' : '' }}>Paiement en attente</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>En traitement</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Expédiée</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Livrée</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                            <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Remboursée</option>
                        </select>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary w-100">
                            Mettre à Jour
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer mt-3 border-top">
                 <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary w-100">
                    <i class="fa fa-arrow-left me-1"></i> Retour à la Liste des Commandes
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add any page-specific JavaScript here if needed
</script>
@endpush
