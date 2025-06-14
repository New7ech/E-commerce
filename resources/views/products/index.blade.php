<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filter Form -->
            <div class="mb-6 p-4 bg-white shadow-sm sm:rounded-lg">
                <form method="GET" action="{{ route('products.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                            <input type="text" name="search" id="search" value="{{ $search ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Categories</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (isset($category) && $category == $cat->id) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-1 flex items-end">
                            <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Product Grid -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($articles->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($articles as $article)
                                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                    @if ($article->image_path)
                                    <img src="{{ Storage::url($article->image_path) }}" alt="{{ $article->name }}" class="w-full h-48 object-cover">
                                    @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">No Image</span>
                                    </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $article->name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $article->categorie->name ?? 'N/A' }}</p>
                                        <p class="text-gray-700 mt-2 text-sm">{{ Str::limit($article->description, 100) }}</p>
                                        <div class="mt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                                            <span class="text-xl font-bold text-gray-900 mb-2 sm:mb-0">${{ number_format($article->prix, 2) }}</span>
                                            <div class="flex flex-col sm:flex-row items-start sm:items-center w-full sm:w-auto">
                                                <a href="{{ route('products.show', $article->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold mb-2 sm:mb-0 sm:mr-2 w-full sm:w-auto text-center sm:text-left">View Details</a>
                                                <form action="{{ route('cart.add', $article->id) }}" method="POST" class="w-full sm:w-auto">
                                                    @csrf
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="w-full sm:w-auto justify-center inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" {{ $article->quantite <= 0 ? 'disabled' : '' }}>
                                                        Add to Cart
                                                    </button>
                                                    @if($article->quantite <= 0)
                                                        <p class="text-xs text-red-500 mt-1 text-center sm:text-left">Out of stock</p>
                                                    @endif
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination Links -->
                        <div class="mt-6">
                            {{ $articles->appends(request()->query())->links() }}
                        </div>
                    @else
                        <p>No products found matching your criteria.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
