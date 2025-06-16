@extends('layouts.app')

@section('title', 'Passer à la Caisse')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Passer à la Caisse</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('home') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('products.index') }}">Produits</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('cart.index') }}">Panier</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Caisse</li>
    </ul>
</div>

<div class="row">
    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form" class="needs-validation" novalidate>
        @csrf
        <div class="col-md-12"> {{-- Main column for layout --}}
            <div class="row">
                {{-- Shipping and Billing Information --}}
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Informations de Livraison et Facturation</h4>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger mb-3">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <h5 class="fw-semibold mb-3">Adresse de Livraison</h5>
                            <div class="row g-3">
                                <div class="col-md-12 form-group">
                                    <label for="shipping_name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" name="shipping_name" id="shipping_name" value="{{ old('shipping_name', auth()->user()->name ?? '') }}" required class="form-control @error('shipping_name') is-invalid @enderror">
                                    @error('shipping_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="shipping_address" class="form-label">Adresse <span class="text-danger">*</span></label>
                                    <input type="text" name="shipping_address" id="shipping_address" value="{{ old('shipping_address') }}" required class="form-control @error('shipping_address') is-invalid @enderror">
                                     @error('shipping_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="shipping_city" class="form-label">Ville <span class="text-danger">*</span></label>
                                    <input type="text" name="shipping_city" id="shipping_city" value="{{ old('shipping_city') }}" required class="form-control @error('shipping_city') is-invalid @enderror">
                                     @error('shipping_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="shipping_postal_code" class="form-label">Code Postal <span class="text-danger">*</span></label>
                                    <input type="text" name="shipping_postal_code" id="shipping_postal_code" value="{{ old('shipping_postal_code') }}" required class="form-control @error('shipping_postal_code') is-invalid @enderror">
                                     @error('shipping_postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="shipping_country" class="form-label">Pays <span class="text-danger">*</span></label>
                                    <input type="text" name="shipping_country" id="shipping_country" value="{{ old('shipping_country') }}" required class="form-control @error('shipping_country') is-invalid @enderror">
                                     @error('shipping_country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                @guest
                                <div class="col-md-6 form-group">
                                    <label for="guest_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="guest_email" id="guest_email" value="{{ old('guest_email') }}" required class="form-control @error('guest_email') is-invalid @enderror">
                                    @error('guest_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                {{-- Optionally, add a phone field for guests if needed --}}
                                {{-- <div class="col-md-6 form-group">
                                    <label for="guest_phone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                    <input type="text" name="guest_phone" id="guest_phone" value="{{ old('guest_phone') }}" required class="form-control @error('guest_phone') is-invalid @enderror">
                                    @error('guest_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div> --}}
                                @endguest

                                @auth
                                 <div class="col-md-6 form-group">
                                    <label for="shipping_phone" class="form-label">Téléphone</label> {{-- Not strictly required for logged in if profile has it, but good for order specific --}}
                                    <input type="text" name="shipping_phone" id="shipping_phone" value="{{ old('shipping_phone', auth()->user()->phone ?? '') }}" class="form-control @error('shipping_phone') is-invalid @enderror">
                                    @error('shipping_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    {{-- For authenticated users, their email is known. This field is for display or if they need a different contact for THIS order --}}
                                    <label for="shipping_email_display" class="form-label">Email de contact (pour cette commande)</label>
                                    <input type="email" name="shipping_email" id="shipping_email_display" value="{{ old('shipping_email', auth()->user()->email ?? '') }}" class="form-control @error('shipping_email') is-invalid @enderror">
                                    @error('shipping_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="form-text text-muted">Votre email de compte est {{ auth()->user()->email }}. Les notifications iront à cet email.</small>
                                </div>
                                @endauth
                            </div>

                            <hr class="my-4">
                            <h5 class="fw-semibold mb-3">Adresse de Facturation</h5>
                            <div class="form-check mb-3">
                                <input type="checkbox" name="billing_same_as_shipping" id="billing_same_as_shipping" value="1" {{ old('billing_same_as_shipping', true) ? 'checked' : '' }} class="form-check-input">
                                <label for="billing_same_as_shipping" class="form-check-label">Identique à l'adresse de livraison</label>
                            </div>

                            <div id="billing_address_form" class="{{ old('billing_same_as_shipping', true) ? 'd-none' : '' }}">
                                <div class="row g-3">
                                    <div class="col-md-12 form-group">
                                        <label for="billing_name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                        <input type="text" name="billing_name" id="billing_name" value="{{ old('billing_name', auth()->user()->name ?? '') }}" class="form-control @error('billing_name') is-invalid @enderror">
                                         @error('billing_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="billing_address" class="form-label">Adresse <span class="text-danger">*</span></label>
                                        <input type="text" name="billing_address" id="billing_address" value="{{ old('billing_address') }}" class="form-control @error('billing_address') is-invalid @enderror">
                                         @error('billing_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="billing_city" class="form-label">Ville <span class="text-danger">*</span></label>
                                        <input type="text" name="billing_city" id="billing_city" value="{{ old('billing_city') }}" class="form-control @error('billing_city') is-invalid @enderror">
                                         @error('billing_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="billing_postal_code" class="form-label">Code Postal <span class="text-danger">*</span></label>
                                        <input type="text" name="billing_postal_code" id="billing_postal_code" value="{{ old('billing_postal_code') }}" class="form-control @error('billing_postal_code') is-invalid @enderror">
                                         @error('billing_postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="billing_country" class="form-label">Pays <span class="text-danger">*</span></label>
                                        <input type="text" name="billing_country" id="billing_country" value="{{ old('billing_country') }}" class="form-control @error('billing_country') is-invalid @enderror">
                                         @error('billing_country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">
                             <h5 class="fw-semibold mb-3">Mode de Paiement</h5>
                             <div class="form-group">
                                 <label for="payment_method" class="form-label">Mode de paiement <span class="text-danger">*</span></label>
                                 <select name="payment_method" id="payment_method" required class="form-select @error('payment_method') is-invalid @enderror">
                                     <option value="mock_payment" {{ old('payment_method') == 'mock_payment' ? 'selected' : '' }}>Simulation de Paiement</option>
                                     {{-- Add other payment methods here if needed --}}
                                     {{-- <option value="stripe" {{ old('payment_method') == 'stripe' ? 'selected' : '' }}>Carte de Crédit (Stripe)</option> --}}
                                 </select>
                                 @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                             </div>
                             <div class="mt-3 p-3 border rounded bg-light">
                                <h6 class="fw-medium">Passerelle de Paiement (Simulation)</h6>
                                <p class="text-muted small">
                                    Ceci est une simulation de l'intégration d'une passerelle de paiement. Aucune transaction réelle ne sera effectuée.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Récapitulatif de la Commande</h4>
                        </div>
                        <div class="card-body">
                            @if (count($articlesInCart) > 0)
                                <ul class="list-group list-group-flush">
                                    @foreach ($articlesInCart as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <div>
                                                <h6 class="mb-0">{{ $item['name'] }}</h6>
                                                <small class="text-muted">Qté: {{ $item['quantity'] }} x {{ number_format($item['prix'], 0, ',', ' ') }} FCFA</small>
                                            </div>
                                            <span class="fw-semibold">{{ number_format($item['subtotal'], 0, ',', ' ') }} FCFA</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <hr class="my-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-semibold">Total</h5>
                                    <h5 class="mb-0 fw-bold text-primary">{{ number_format($totalPrice, 0, ',', ' ') }} FCFA</h5>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-success btn-lg btn-round w-100">
                                        <i class="fa fa-lock me-2"></i> Confirmer et Payer
                                    </button>
                                </div>
                            @else
                                <p class="text-center">Votre panier est vide.</p>
                                <a href="{{ route('products.index') }}" class="btn btn-primary btn-round w-100 mt-2">Continuer les Achats</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('billing_same_as_shipping');
    const billingForm = document.getElementById('billing_address_form');
    const billingInputs = billingForm.querySelectorAll('input, select, textarea'); // Include all relevant form elements

    function toggleBillingForm() {
        const isSameAsShipping = checkbox.checked;
        if (isSameAsShipping) {
            billingForm.classList.add('d-none'); // Bootstrap 5 hide class
            billingInputs.forEach(input => {
                // Keep original required status if form is hidden
                if (input.dataset.originalRequired === 'true') {
                    input.required = true;
                } else {
                    input.required = false;
                }
            });
        } else {
            billingForm.classList.remove('d-none');
            billingInputs.forEach(input => {
                // Store original required status if not already stored
                if (typeof input.dataset.originalRequired === 'undefined') {
                    input.dataset.originalRequired = input.required;
                }
                // Set required based on its original status when shown
                 if (input.dataset.originalRequired === 'true') {
                    input.required = true;
                }
            });
        }
    }

    // Store initial required status for all billing inputs
    billingInputs.forEach(input => {
        input.dataset.originalRequired = input.required;
    });


    checkbox.addEventListener('change', toggleBillingForm);
    // Initialize form state on page load
    toggleBillingForm();

    // Bootstrap 5 form validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
});
</script>
@endpush
