<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Shipping and Billing Information -->
                    <div class="md:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4">Shipping Information</h3>
                            @if ($errors->any())
                                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                                    <ul class="list-disc pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 gap-y-6">
                                <div>
                                    <label for="shipping_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" name="shipping_name" id="shipping_name" value="{{ old('shipping_name', auth()->user()->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label for="shipping_address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <input type="text" name="shipping_address" id="shipping_address" value="{{ old('shipping_address') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label for="shipping_city" class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" name="shipping_city" id="shipping_city" value="{{ old('shipping_city') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                                        <input type="text" name="shipping_postal_code" id="shipping_postal_code" value="{{ old('shipping_postal_code') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                    <div>
                                        <label for="shipping_country" class="block text-sm font-medium text-gray-700">Country</label>
                                        <input type="text" name="shipping_country" id="shipping_country" value="{{ old('shipping_country') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-6">

                            <h3 class="text-lg font-semibold mb-4">Billing Information</h3>
                            <div>
                                <input type="checkbox" name="billing_same_as_shipping" id="billing_same_as_shipping" value="1" {{ old('billing_same_as_shipping', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <label for="billing_same_as_shipping" class="ml-2 text-sm text-gray-900">Same as shipping address</label>
                            </div>

                            <div id="billing_address_form" class="{{ old('billing_same_as_shipping', true) ? 'hidden' : '' }} mt-4 grid grid-cols-1 gap-y-6">
                                <div>
                                    <label for="billing_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" name="billing_name" id="billing_name" value="{{ old('billing_name', auth()->user()->name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label for="billing_address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <input type="text" name="billing_address" id="billing_address" value="{{ old('billing_address') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label for="billing_city" class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" name="billing_city" id="billing_city" value="{{ old('billing_city') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="billing_postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                                        <input type="text" name="billing_postal_code" id="billing_postal_code" value="{{ old('billing_postal_code') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                    <div>
                                        <label for="billing_country" class="block text-sm font-medium text-gray-700">Country</label>
                                        <input type="text" name="billing_country" id="billing_country" value="{{ old('billing_country') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-6">
                             <h3 class="text-lg font-semibold mb-4">Payment Method</h3>
                             <div>
                                 <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                                 <select name="payment_method" id="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                     <option value="mock_payment">Mock Payment Gateway</option>
                                     {{-- Add other payment methods here if needed --}}
                                 </select>
                             </div>
                             <div class="mt-6 p-4 border rounded-lg bg-gray-50">
                                <h4 class="text-md font-semibold text-gray-700">Payment Gateway Placeholder</h4>
                                <p class="text-sm text-gray-600 mt-2">
                                    This is where the actual payment gateway integration (e.g., Stripe Elements, PayPal button) would appear.
                                    For now, selecting "Mock Payment Gateway" will simulate a successful payment.
                                </p>
                            </div>


                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="md:col-span-1">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4">Order Summary</h3>
                            @if (count($articlesInCart) > 0)
                                <ul class="divide-y divide-gray-200">
                                    @foreach ($articlesInCart as $item)
                                        <li class="py-4 flex justify-between items-center">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $item['name'] }}</p>
                                                <p class="text-xs text-gray-500">Qty: {{ $item['quantity'] }} x ${{ number_format($item['prix'], 2) }}</p>
                                            </div>
                                            <p class="text-sm font-medium text-gray-900">${{ number_format($item['subtotal'], 2) }}</p>
                                        </li>
                                    @endforeach
                                </ul>
                                <hr class="my-4">
                                <div class="flex justify-between items-center font-semibold text-lg">
                                    <p>Total</p>
                                    <p>${{ number_format($totalPrice, 2) }}</p>
                                </div>
                                <div class="mt-6">
                                    <button type="submit" class="w-full justify-center inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Place Order
                                    </button>
                                </div>
                            @else
                                <p>Your cart is empty.</p>
                                <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold mt-2 inline-block">Continue Shopping</a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkbox = document.getElementById('billing_same_as_shipping');
            const billingForm = document.getElementById('billing_address_form');
            const billingInputs = billingForm.querySelectorAll('input');

            function toggleBillingForm() {
                if (checkbox.checked) {
                    billingForm.classList.add('hidden');
                    billingInputs.forEach(input => input.required = false);
                } else {
                    billingForm.classList.remove('hidden');
                    billingInputs.forEach(input => {
                        if(input.name !== '_token') { // Don't make token required
                           input.required = true;
                        }
                    });
                }
            }

            checkbox.addEventListener('change', toggleBillingForm);
            // Initialize form state on page load
            toggleBillingForm();
        });
    </script>
</x-app-layout>
