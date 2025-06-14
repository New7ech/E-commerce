<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $article->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            @if ($article->image_path)
                                <img src="{{ Storage::url($article->image_path) }}" alt="{{ $article->name }}" class="w-full h-auto object-cover rounded-lg shadow-md">
                            @else
                                <div class="w-full p-4 border rounded-lg bg-gray-50">
                                    <div class="flex items-center justify-center h-64 bg-gray-200 rounded-md">
                                        <span class="text-gray-500">No Image Available</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $article->name }}</h1>

                            <p class="text-md text-gray-600 mt-2">
                                Category: <span class="font-semibold">{{ $article->categorie->name ?? 'N/A' }}</span>
                            </p>

                            <p class="text-2xl font-semibold text-indigo-600 mt-4">${{ number_format($article->prix, 2) }}</p>

                            @if($article->quantite > 0)
                                <p class="text-sm text-green-600 mt-2">In Stock ({{ $article->quantite }} available)</p>
                            @else
                                <p class="text-sm text-red-600 mt-2">Out of Stock</p>
                            @endif

                            <div class="mt-6">
                                <h3 class="text-lg font-medium text-gray-900">Description</h3>
                                <p class="text-gray-700 mt-2 text-sm leading-relaxed">
                                    {{ $article->description ?? 'No description available.' }}
                                </p>
                            </div>

                            <div class="mt-6">
                                <h3 class="text-lg font-medium text-gray-900">Additional Details</h3>
                                <ul class="list-disc list-inside mt-2 text-sm text-gray-700">
                                    <li>Fournisseur: {{ $article->fournisseur->name ?? 'N/A' }}</li>
                                    <li>Emplacement: {{ $article->emplacement->name ?? 'N/A' }}</li>
                                    {{-- Add other relevant details from the Article model as needed --}}
                                </ul>
                            </div>

                            <div class="mt-8">
                                <form action="{{ route('cart.add', $article->id) }}" method="POST">
                                    @csrf
                                    <div class="flex items-center">
                                        <label for="quantity" class="mr-2 text-sm font-medium text-gray-700">Quantity:</label>
                                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $article->quantite > 0 ? $article->quantite : 1 }}" class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ $article->quantite <= 0 ? 'disabled' : '' }}>
                                    </div>
                                    <button type="submit" class="mt-4 w-full justify-center inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" {{ $article->quantite <= 0 ? 'disabled' : '' }}>
                                        Add to Cart
                                    </button>
                                </form>
                                @if($article->quantite <= 0)
                                <p class="text-xs text-red-500 mt-1 text-center">This item is currently out of stock.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr; Back to Products</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
