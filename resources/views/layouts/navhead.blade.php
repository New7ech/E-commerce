<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
    <div class="container-fluid">
        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
            <form class="form-inline w-100" method="GET" action="{{ route('products.index') }}">
                <div class="input-group w-100">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher des produits..." aria-label="Rechercher des produits">
                    <button class="btn btn-primary" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>
        </nav>
        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

            <li class="nav-item dropdown hidden-caret">
                <a class="nav-link" href="{{ route('cart.index') }}" title="Panier">
                    <i class="fas fa-shopping-cart"></i>
                    @php
                        $cart = session()->get('cart', []);
                        $cartItemCount = 0;
                        if (is_array($cart)) {
                            foreach ($cart as $id => $details) {
                                if (is_array($details)) {
                                    $cartItemCount += $details['quantity'] ?? 0;
                                }
                            }
                        }
                    @endphp
                    @if ($cartItemCount > 0)
                        <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" style="font-size: 0.6em; padding: 0.3em 0.5em;">
                            {{ $cartItemCount }}
                        </span>
                    @endif
                </a>
            </li>

            <li class="nav-item dropdown hidden-caret">
                <a class="nav-link" href="{{ route('wishlist.index') }}" title="Liste de souhaits">
                    <i class="fas fa-heart"></i>
                    {{-- Optionally, add a count for wishlist items if available --}}
                </a>
            </li>

            @include('layouts.notification')

            <li class="nav-item topbar-user dropdown hidden-caret">
                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <div class="avatar-sm">
                        <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('assets/img/kaiadmin/logocommerce.PNG') }}" alt="Image de Profil" class="avatar-img rounded-circle" />
                    </div>
                    <span class="profile-username">
                        <span class="fw-bold">{{ Auth::user()->name ?? 'Utilisateur' }}</span>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                        <li>
                            <div class="user-box d-flex align-items-center">
                                <div class="avatar-lg me-3">
                                     <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('assets/img/kaiadmin/logocommerce.PNG') }}" alt="Image de Profil" class="avatar-img rounded" />
                                </div>
                                <div class="u-text">
                                    <h4>{{ Auth::user()->name ?? 'Utilisateur' }}</h4>
                                    <p class="text-muted mb-1">{{ Auth::user()->email ?? '' }}</p>
                                    <a class="btn btn-xs btn-secondary btn-sm" href="{{ route('profile.edit') }}">Voir le Profil</a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-circle me-2"></i> Mon Profil</a>
                            <a class="dropdown-item" href="{{ route('profile.orders') }}"><i class="fas fa-history me-2"></i> Mes Commandes</a>
                            <a class="dropdown-item" href="#"> <!-- Placeholder for user settings/activity log -->
                                <i class="fas fa-cog me-2"></i> Paramètres du compte
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('custom.logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                               <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                            </a>
                            <form id="logout-form" action="{{ route('custom.logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </div>
                </ul>
            </li>
        </ul>
    </div>
</nav>
