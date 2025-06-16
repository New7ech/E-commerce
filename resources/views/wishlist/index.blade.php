@extends('layouts.app')

@section('title', 'Ma Liste de Souhaits')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Ma Liste de Souhaits</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('home') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('profile.edit') ?? '#' }}">Mon Compte</a></li> {{-- Assuming profile route exists --}}
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Liste de Souhaits</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Articles dans ma liste de souhaits</h4>
            </div>
            <div class="card-body">
                @if ($wishlistItems->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Produit</th>
                                    <th scope="col">Prix</th>
                                    <th scope="col">Ajouté le</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($wishlistItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('products.show', $item->article->id) }}">
                                                    @if ($item->article->image_path && Storage::disk('public')->exists($item->article->image_path))
                                                        <img src="{{ Storage::url($item->article->image_path) }}" alt="{{ $item->article->name }}" class="img-fluid rounded me-3" style="width: 75px; height: 75px; object-fit: cover;">
                                                    @else
                                                        <img src="{{ asset('assets/img/placeholder-product.jpg') }}" alt="Placeholder" class="img-fluid rounded me-3" style="width: 75px; height: 75px; object-fit: cover;">
                                                    @endif
                                                </a>
                                                <div>
                                                    <h6 class="mb-0"><a href="{{ route('products.show', $item->article->id) }}" class="text-dark text-decoration-none">{{ $item->article->name }}</a></h6>
                                                    <small class="text-muted">{{ $item->article->categorie->name ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle fw-semibold">{{ number_format($item->article->prix, 0, ',', ' ') }} FCFA</td>
                                        <td class="align-middle">{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td class="align-middle">
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('products.show', $item->article->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-eye"></i> Voir
                                                </a>
                                                <form action="{{ route('wishlist.remove', $item->article->id) }}" method="POST" class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fa fa-trash"></i> Retirer
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links --}}
                    @if ($wishlistItems->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $wishlistItems->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-heart-broken fa-3x text-muted mb-3"></i>
                        <p class="fs-5 text-muted">Votre liste de souhaits est vide.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary btn-round">Découvrir des produits</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
