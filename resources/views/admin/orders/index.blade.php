@extends('layouts.app') {{-- Changed from x-app-layout --}}

@section('title', 'Gestion des Commandes')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Commandes</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li> {{-- Assuming dashboard route --}}
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Commandes</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        @if (session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Filtrer les Commandes</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.orders.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Recherche (ID Commande, Client)</label>
                            <input type="text" name="search" id="search" value="{{ $search ?? '' }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Statut de la Commande</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Tous les Statuts</option>
                                <option value="pending_payment" {{ (isset($status) && $status == 'pending_payment') ? 'selected' : '' }}>Paiement en attente</option>
                                <option value="processing" {{ (isset($status) && $status == 'processing') ? 'selected' : '' }}>En traitement</option>
                                <option value="shipped" {{ (isset($status) && $status == 'shipped') ? 'selected' : '' }}>Expédiée</option>
                                <option value="delivered" {{ (isset($status) && $status == 'delivered') ? 'selected' : '' }}>Livrée</option>
                                <option value="cancelled" {{ (isset($status) && $status == 'cancelled') ? 'selected' : '' }}>Annulée</option>
                                <option value="refunded" {{ (isset($status) && $status == 'refunded') ? 'selected' : '' }}>Remboursée</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-filter me-1"></i> Filtrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Liste des Commandes</h4>
                {{-- Add "New Order" button if applicable for admin --}}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="admin-orders-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID Commande</th>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Statut Paiement</th>
                                <th>Statut Commande</th>
                                <th style="width: 10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>
                                        {{ $order->user->name ?? $order->shipping_name ?? 'N/A' }} <br>
                                        <small class="text-muted">{{ $order->user->email ?? '' }}</small>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <span class="badge
                                            @if($order->payment_status == 'paid') bg-success
                                            @elseif($order->payment_status == 'pending') bg-warning text-dark
                                            @else bg-danger @endif">
                                            {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($order->status == 'delivered' || $order->status == 'shipped') bg-info
                                            @elseif($order->status == 'processing') bg-primary
                                            @elseif($order->status == 'pending_payment') bg-warning text-dark
                                            @elseif($order->status == 'cancelled' || $order->status == 'refunded') bg-danger
                                            @else bg-secondary @endif">
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="form-button-action">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" data-bs-toggle="tooltip" title="Voir" class="btn btn-link btn-primary btn-lg">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            {{-- Add Edit button if admin can edit orders --}}
                                            {{-- <a href="{{ route('admin.orders.edit', $order->id) }}" data-bs-toggle="tooltip" title="Modifier" class="btn btn-link btn-warning btn-lg">
                                                <i class="fa fa-edit"></i>
                                            </a> --}}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Aucune commande trouvée.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-center">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#admin-orders-table').DataTable({
            "searching": false, // Disabled because we have a custom filter form
            "paging": false,    // Disabled to use Laravel's pagination
            "info": false,      // Disabled to use Laravel's pagination
            // "order": [[2, "desc"]] // Example: order by date desc by default
        });
    });
</script>
@endpush
