@extends('layouts.app')

@section('title', 'Panier d\'Achat')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Panier d'Achat</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('products.index') }}">Produits</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Panier</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash') {{-- For success/error messages --}}
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Votre Panier</h4>
            </div>
            <div class="card-body">
                @if (count($articlesInCart) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover" id="cart-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Produit</th>
                                    <th scope="col" class="text-center">Prix</th>
                                    <th scope="col" class="text-center" style="width: 150px;">Quantité</th>
                                    <th scope="col" class="text-end">Sous-total</th>
                                    <th scope="col" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($articlesInCart as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                {{-- Placeholder for image - consider adding if available
                                                <img src="{{ $item['image_url'] ?? asset('assets/img/placeholder-product.jpg') }}" alt="{{ $item['name'] }}" style="width: 60px; height: 60px; object-fit: cover;" class="me-3 rounded">
                                                --}}
                                                <div>
                                                    <h6 class="fw-semibold mb-0"><a href="{{ route('products.show', $item['id']) }}">{{ $item['name'] }}</a></h6>
                                                    {{-- <small class="text-muted">SKU: {{ $item['id'] }}</small> --}}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">{{ number_format($item['prix'], 0, ',', ' ') }} FCFA</td>
                                        <td class="text-center align-middle">
                                            <form action="{{ route('cart.update', $item['id']) }}" method="POST" class="d-inline-flex align-items-center">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm text-center" style="width: 70px;">
                                                <button type="submit" class="btn btn-primary btn-sm ms-2" data-bs-toggle="tooltip" title="Mettre à jour">
                                                    <i class="fa fa-sync"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-end align-middle fw-semibold">{{ number_format($item['subtotal'], 0, ',', ' ') }} FCFA</td>
                                        <td class="text-center align-middle">
                                            <form action="{{ route('cart.remove', $item['id']) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Supprimer">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-8">
                            <form action="{{ route('cart.clear') }}" method="POST" class="mb-3">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Voulez-vous vraiment vider votre panier ?');">
                                    <i class="fa fa-times-circle me-1"></i> Vider le Panier
                                </button>
                            </form>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-arrow-left me-1"></i> Continuer les Achats
                            </a>
                        </div>
                        <div class="col-md-4 text-end">
                            <h4 class="fw-bold">Total du Panier</h4>
                            {{-- Assuming no complex tax/shipping calculation in cart view itself --}}
                            <p class="fs-4 fw-bold mb-2">{{ number_format($totalPrice, 0, ',', ' ') }} FCFA</p>
                            <a href="{{ route('checkout.index') }}" class="btn btn-success btn-lg btn-round w-100">
                                <i class="fa fa-credit-card me-2"></i> Passer à la Caisse
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="fs-5 text-muted">Votre panier est vide.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary btn-round mt-3">
                           <i class="fa fa-store me-1"></i> Commencer vos Achats
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize tooltips for dynamically added buttons if any, or static ones
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
