<x-app-layout> {{-- Assuming you have an admin layout --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Details') }} #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
             @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Order Details and Items -->
                <div class="md:col-span-2">
                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Order #{{ $order->id }} - {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                Placed on: {{ $order->created_at->format('M d, Y H:i A') }}
                            </p>
                        </div>
                        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                            <dl class="sm:divide-y sm:divide-gray-200">
                                <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Customer</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $order->user->name ?? 'Guest User' }} <br>
                                        {{ $order->user->email ?? '' }}
                                    </dd>
                                </div>
                                <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">${{ number_format($order->total_amount, 2) }}</dd>
                                </div>
                                <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->payment_method }}</dd>
                                </div>
                                <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($order->payment_status == 'paid') bg-green-100 text-green-800 @elseif($order->payment_status == 'pending') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Shipping Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $order->shipping_name }}<br>
                                        {{ $order->shipping_address }}<br>
                                        {{ $order->shipping_city }}, {{ $order->shipping_postal_code }}<br>
                                        {{ $order->shipping_country }}
                                    </dd>
                                </div>
                                @if($order->billing_name)
                                <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Billing Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $order->billing_name }}<br>
                                        {{ $order->billing_address }}<br>
                                        {{ $order->billing_city }}, {{ $order->billing_postal_code }}<br>
                                        {{ $order->billing_country }}
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                             <h4 class="text-md font-semibold text-gray-700 mb-3">Order Items</h4>
                             <ul role="list" class="divide-y divide-gray-200">
                                @foreach($order->items as $item)
                                <li class="py-4 flex">
                                    {{-- Placeholder for image: <img src="{{ $item->article->image_url ?? 'https://via.placeholder.com/100' }}" alt="{{ $item->article->name }}" class="h-16 w-16 rounded-md object-cover"> --}}
                                    <div class="ml-0 flex-1 flex flex-col"> {{-- Changed ml-4 to ml-0 if no image --}}
                                        <div>
                                            <div class="flex justify-between text-base font-medium text-gray-900">
                                                <h3>
                                                    <a href="{{ route('products.show', $item->article->id) }}" target="_blank">{{ $item->article->name }}</a>
                                                </h3>
                                                <p class="ml-4">${{ number_format($item->price * $item->quantity, 2) }}</p>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-500">SKU: {{ $item->article->id }}</p> {{-- Assuming SKU is article ID for now --}}
                                        </div>
                                        <div class="flex-1 flex items-end justify-between text-sm">
                                            <p class="text-gray-500">Qty: {{ $item->quantity }}</p>
                                            <p class="text-gray-500">Unit Price: ${{ number_format($item->price, 2) }}</p>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Actions and Status Update -->
                <div class="md:col-span-1">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Update Order Status</h3>
                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Order Status</label>
                                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="pending_payment" {{ $order->status === 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Update Status
                                </button>
                            </div>
                        </form>
                         <div class="mt-6 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.orders.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr; Back to Orders List</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
