<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Order History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
             {{-- Navigation for Profile Section --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('profile.edit') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold {{ request()->routeIs('profile.edit') ? 'underline' : '' }}">
                        {{ __('Edit Profile') }}
                    </a>
                    <a href="{{ route('profile.orders') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold {{ request()->routeIs('profile.orders') ? 'underline' : '' }}">
                        {{ __('Order History') }}
                    </a>
                    {{-- Add other profile related links here if needed --}}
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($orders->count() > 0)
                        <div class="space-y-6">
                            @foreach ($orders as $order)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h3 class="text-lg font-semibold">Order #{{ $order->id }}</h3>
                                            <p class="text-sm text-gray-600">Placed on: {{ $order->created_at->format('F d, Y H:i') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-semibold">${{ number_format($order->total_amount, 2) }}</p>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($order->status == 'delivered' || $order->status == 'shipped') bg-blue-100 text-blue-800
                                                @elseif($order->status == 'processing') bg-indigo-100 text-indigo-800
                                                @elseif($order->status == 'pending_payment') bg-yellow-100 text-yellow-800
                                                @elseif($order->status == 'cancelled' || $order->status == 'refunded') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <h4 class="text-md font-medium text-gray-700">Items:</h4>
                                        <ul class="list-disc list-inside ml-4 mt-2 text-sm text-gray-600">
                                            @foreach ($order->items as $item)
                                                <li>{{ $item->article->name }} (Qty: {{ $item->quantity }}) - ${{ number_format($item->price, 2) }} each</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                     <div class="mt-4 text-sm">
                                        <p><strong>Shipping Address:</strong> {{ $order->shipping_name }}, {{ $order->shipping_address }}, {{ $order->shipping_city }}, {{ $order->shipping_postal_code }}, {{ $order->shipping_country }}</p>
                                        <p><strong>Payment Status:</strong> <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span></p>
                                    </div>
                                    {{-- Optionally add a link to a more detailed order view if needed --}}
                                    {{-- <div class="mt-4">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900">View Order Details</a>
                                    </div> --}}
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <p>You have not placed any orders yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
