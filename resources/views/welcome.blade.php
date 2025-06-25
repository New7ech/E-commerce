@extends('layouts.app') {{-- Assurez-vous que ce layout existe et est approprié --}}

@section('title', 'Accueil - Bienvenue sur Notre Boutique')

@push('styles')
<style>
    /* Styles généraux */
    body {
        font-family: 'Arial', sans-serif;
        color: #333;
        line-height: 1.6;
    }

    .navbar-custom {
        background-color: #232f3e;
        padding: 0.5rem 1rem;
        border-bottom: 1px solid #3b4a5a;
    }

    .navbar-custom .navbar-brand img {
        height: 40px; /* Ajustez la taille du logo */
    }

    .navbar-custom .form-control {
        background-color: #fff;
        border-color: #ced4da;
        width: 100%; /* Barre de recherche prend plus de place */
    }
    .navbar-custom .btn-search {
        background-color: #febd69;
        border-color: #febd69;
        color: #111;
    }
    .navbar-custom .btn-search:hover {
        background-color: #f3a847;
        border-color: #f3a847;
    }

    .nav-categories {
        background-color: #37475a;
        padding: 0.5rem 1rem;
    }
    .nav-categories .nav-link {
        color: #fff;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    .nav-categories .nav-link:hover {
        color: #febd69;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); /* Responsive grid */
        gap: 1.5rem;
        padding: 2rem 0;
    }

    .product-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        transition: box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }

    .product-card-img-top {
        width: 100%;
        height: 200px; /* Hauteur fixe pour l'image */
        object-fit: cover; /* Assure que l'image couvre bien sans être déformée */
    }

    .product-card-body {
        padding: 1rem;
        flex-grow: 1; /* Permet au corps de la carte de grandir */
        display: flex;
        flex-direction: column;
    }

    .product-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #007bff; /* Couleur de titre pour attirer l'attention */
    }

    .product-card-short-description {
        font-size: 0.85rem;
        color: #555;
        margin-bottom: 0.75rem;
        flex-grow: 1; /* Permet à la description de prendre l'espace disponible */
    }

    .product-card-price {
        font-size: 1.2rem;
        font-weight: bold;
        color: #B12704; /* Couleur de prix style Amazon */
        margin-bottom: 1rem;
    }

    .product-card .btn {
        margin-top: auto; /* Aligne les boutons en bas */
        margin-right: 0.5rem;
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
    .product-card .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .product-card .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }


    .site-footer {
        background-color: #232f3e;
        color: #ddd;
        padding: 3rem 0;
        font-size: 0.9rem;
    }
    .site-footer h5 {
        color: #fff;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }
    .site-footer a {
        color: #ddd;
        text-decoration: none;
    }
    .site-footer a:hover {
        color: #febd69;
        text-decoration: underline;
    }
    .site-footer .list-unstyled li {
        margin-bottom: 0.5rem;
    }
    .social-icons a {
        font-size: 1.5rem;
        margin-right: 1rem;
        color: #fff;
    }
    .social-icons a:hover {
        color: #febd69;
    }
    .footer-bottom {
        background-color: #131a22;
        padding: 1rem 0;
        text-align: center;
        font-size: 0.8rem;
        color: #aaa;
    }

    /* Pagination styling */
    .pagination {
        justify-content: center;
        margin-top: 2rem;
    }
    .pagination .page-item .page-link {
        color: #007bff;
    }
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
    }

</style>
{{-- Inclure FontAwesome pour les icônes --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

@section('content')
<header>
    {{-- Navbar principale --}}
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/logoproduct3.svg') }}" alt="Logo Fictif"> {{-- Assurez-vous que ce logo existe --}}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="color: #fff;"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <form class="d-flex ms-auto my-2 my-lg-0 flex-grow-1 px-md-5" role="search">
                    <input class="form-control me-2" type="search" placeholder="Rechercher des produits..." aria-label="Search">
                    <button class="btn btn-search" type="submit"><i class="fas fa-search"></i></button>
                </form>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}" style="color: #fff;">Connexion</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}" style="color: #fff;">Inscription</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #fff;">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profil</a></li>
                                <li><a class="dropdown-item" href="#">Mes commandes</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('custom.logout') }}">
                                        @csrf
                                        <a class="dropdown-item" href="{{ route('custom.logout') }}"
                                           onclick="event.preventDefault(); this.closest('form').submit();">
                                            Déconnexion
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/cart') }}" style="color: #fff;">
                            <i class="fas fa-shopping-cart"></i> Panier <span class="badge bg-danger">0</span> {{-- Le nombre d'articles sera dynamique plus tard --}}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Navbar des catégories --}}
    <nav class="nav-categories">
        <div class="container-fluid">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Accueil</a>
                </li>
                @if(isset($categories) && $categories->count())
                    @foreach($categories->take(5) as $categorie) {{-- Afficher seulement les 5 premières catégories par exemple --}}
                        <li class="nav-item"><a class="nav-link" href="#">{{ $categorie->name }}</a></li>
                    @endforeach
                @endif
                <li class="nav-item"><a class="nav-link" href="#">Offres</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Nouveautés</a></li>
                <li class="nav-item dropdown ms-auto"> {{-- Menu déroulant pour plus de catégories --}}
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMoreCategories" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Plus de Catégories
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMoreCategories">
                        @if(isset($categories) && $categories->count() > 5)
                            @foreach($categories->slice(5) as $categorie)
                                <li><a class="dropdown-item" href="#">{{ $categorie->name }}</a></li>
                            @endforeach
                        @else
                            <li><a class="dropdown-item" href="#">Pas d'autres catégories</a></li>
                        @endif
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<main class="container my-4">
    <h1 class="mb-4">Nos Produits</h1>

    @if(isset($articles) && $articles->count())
        <div class="product-grid">
            @foreach($articles as $article)
            <div class="product-card">
                <img src="{{ $article->image_url ?: asset('assets/img/placeholder.jpg') }}" class="product-card-img-top" alt="{{ $article->name }}">
                <div class="product-card-body">
                    <h5 class="product-card-title">{{ $article->name }}</h5>
                    <p class="product-card-short-description">{{ Str::limit($article->short_description ?: $article->description, 100) }}</p>
                    <p class="product-card-price">{{ number_format($article->prix, 2, ',', ' ') }} FCFA</p>
                    <div class="mt-auto"> {{-- Assure que les boutons sont en bas --}}
                        <a href="#" class="btn btn-primary btn-sm">Voir plus</a>
                        <a href="#" class="btn btn-success btn-sm">Ajouter au panier</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $articles->links() }}
        </div>

    @else
        <div class="alert alert-info text-center" role="alert">
            Aucun article disponible pour le moment. Revenez bientôt !
        </div>
    @endif
</main>

<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h5>À propos de nous</h5>
                <ul class="list-unstyled">
                    <li><a href="#">Notre entreprise</a></li>
                    <li><a href="#">Carrières</a></li>
                    <li><a href="#">Presse</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Service Client</h5>
                <ul class="list-unstyled">
                    <li><a href="#">Aide & FAQ</a></li>
                    <li><a href="#">Contactez-nous</a></li>
                    <li><a href="#">Suivi de commande</a></li>
                    <li><a href="#">Retours & Échanges</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Politiques</h5>
                <ul class="list-unstyled">
                    <li><a href="#">Conditions d'utilisation</a></li>
                    <li><a href="#">Politique de confidentialité</a></li>
                    <li><a href="#">Politique de cookies</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Contact & Réseaux</h5>
                <p>
                    123 Rue Imaginaire<br>
                    VilleExemple, 00000<br>
                    Email: contact@maboutique.com<br>
                    Téléphone: +00 123 456 789
                </p>
                <div class="social-icons">
                    <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="footer-bottom">
    <p>&copy; {{ date('Y') }} Ma Boutique Fictive. Tous droits réservés.</p>
</div>

@endsection

@push('scripts')
{{-- Si vous avez besoin de JS spécifique pour cette page --}}
<script>
    // Petit script pour rendre le dropdown du user fonctionnel si Bootstrap JS est chargé via app.js
    // document.addEventListener('DOMContentLoaded', function () {
    //     var userDropdown = document.getElementById('navbarDropdownUser');
    //     if (userDropdown) {
    //         new bootstrap.Dropdown(userDropdown);
    //     }
    //     var categoriesDropdown = document.getElementById('navbarDropdownMoreCategories');
    //     if(categoriesDropdown) {
    //         new bootstrap.Dropdown(categoriesDropdown);
    //     }
    // });
</script>
@endpush
