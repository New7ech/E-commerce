@extends('layouts.app')

@section('title', 'Historique des Commandes')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Historique des Commandes</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li> {{-- Assuming dashboard or similar for home route --}}
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('profile.edit') }}">Profil</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Historique des Commandes</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        {{-- Navigation for Profile Section --}}
        <div class="card">
            <div class="card-body">
                <nav class="nav nav-pills nav-fill">
                    <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                        Modifier le Profil
                    </a>
                    <a class="nav-link {{ request()->routeIs('profile.orders') ? 'active' : '' }}" href="{{ route('profile.orders') }}">
                        Historique des Commandes
                    </a>
                    {{-- Add other profile related links here if needed --}}
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Mes Commandes</h4>
            </div>
            <div class="card-body">
                @if ($orders->count() > 0)
                    <div class="list-group">
                        @foreach ($orders as $order)
                            <div class="list-group-item list-group-item-action mb-3 border rounded">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 fw-bold">Commande #{{ $order->id }}</h5>
                                    <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-8">
                                        <p class="mb-1"><strong>Statut:</strong>
                                            <span class="badge
                                                @if($order->status == 'delivered' || $order->status == 'shipped') bg-success
                                                @elseif($order->status == 'processing') bg-info
                                                @elseif($order->status == 'pending_payment') bg-warning text-dark
                                                @elseif($order->status == 'cancelled' || $order->status == 'refunded') bg-danger
                                                @else bg-secondary @endif">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </p>
                                        <p class="mb-1"><strong>Total:</strong> {{ number_format($order->total_amount, 2, ',', ' ') }} FCFA</p> {{-- Assuming FCFA or similar currency --}}
                                        <p class="mb-1"><strong>Statut Paiement:</strong> <span class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span></p>
                                    </div>
                                    <div class="col-md-4 text-md-end align-self-center">
                                        {{-- <a href="#" class="btn btn-primary btn-sm">Voir Détails</a> --}}
                                        {{-- Placeholder for a dedicated order detail view if it exists --}}
                                    </div>
                                </div>
                                @if($order->items->count() > 0)
                                <div class="mt-3">
                                    <h6 class="fw-semibold">Articles:</h6>
                                    <ul class="list-unstyled small">
                                        @foreach ($order->items as $item)
                                            <li>{{ $item->article->name ?? 'Article non disponible' }} (Qté: {{ $item->quantity }}) - {{ number_format($item->price, 2, ',', ' ') }} FCFA l'unité</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                <div class="mt-2 text-sm">
                                    <p class="mb-0"><strong>Adresse de livraison:</strong> {{ $order->shipping_name }}, {{ $order->shipping_address }}, {{ $order->shipping_city }}, {{ $order->shipping_postal_code }}, {{ $order->shipping_country }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $orders->links() }} {{-- For pagination --}}
                    </div>
                @else
                    <p class="text-center">Vous n'avez passé aucune commande pour le moment.</p>
                @endif
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
