@extends('layouts.app')

@section('title', 'Aide - Création Utilisateur Avancée')

@section('content') {{-- Original was already 'content' --}}

<div class="page-header">
    <h3 class="fw-bold mb-3">Création Avancée d'Utilisateur (Aide)</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('users.index') }}">Utilisateurs</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Création Avancée (Aide)</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        {{-- Removed original container and page-inner as page-header and row/col-md-12 will structure it --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data"> {{-- Assuming this form still posts to users.store --}}
            @csrf

            {{-- Section principale - now a single card containing the two columns --}}
            <div class="card">
                <div class="card-header"><h4 class="card-title">Informations Principales</h4></div>
                <div class="card-body">
                    <div class="row g-3"> {{-- Using g-3 for consistent spacing --}}
                        {{-- Colonne gauche --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">Nom complet</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required pattern=".{3,50}" title="3 à 50 caractères">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="exemple@domain.com">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                             <div class="form-group">
                                <label class="form-label required">Confirmation de l'Email</label>
                                <input type="email" name="email_confirmation" class="form-control @error('email_confirmation') is-invalid @enderror" required placeholder="Confirmez l'email">
                                @error('email_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Téléphone</label>
                                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required pattern="[+0-9\s]{8,20}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Colonne droite --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">Mot de passe</label>
                                <input type="password" name="password" id="password_aide" class="form-control @error('password') is-invalid @enderror" required minlength="8" data-password-rules> {{-- Added id for potential JS hook --}}
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @else <small class="form-text text-muted">Minimum 8 caractères avec majuscule, chiffre et symbole.</small> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Confirmation du mot de passe</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Date de naissance</label>
                                <input type="date" name="birthdate" class="form-control @error('birthdate') is-invalid @enderror" value="{{ old('birthdate') }}">
                                @error('birthdate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="photo_aide" class="form-label">Photo de profil</label> {{-- Changed id for label --}}
                                <input type="file" name="photo" id="photo_aide" class="form-control-file @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/webp"> {{-- form-control-file can be just form-control in BS5 --}}
                                @error('photo') <div class="invalid-feedback d-block">{{ $message }}</div> @else <small class="form-text text-muted">Formats: JPG, PNG, WEBP (max 2MB)</small> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3"> {{-- Moved address outside the two columns for full width --}}
                        <label class="form-label">Adresse complète</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>


            {{-- Section supplémentaire avec des onglets --}}
            <div class="card mt-4">
                <div class="card-header">
                     <ul class="nav nav-tabs card-header-tabs" id="nav-tab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="nav-roles-tab" data-bs-toggle="tab" data-bs-target="#nav-roles" type="button" role="tab" aria-controls="nav-roles" aria-selected="true">Rôles & Permissions</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="nav-preferences-tab" data-bs-toggle="tab" data-bs-target="#nav-preferences" type="button" role="tab" aria-controls="nav-preferences" aria-selected="false">Préférences</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="nav-tabContent">
                        {{-- Onglet Rôles --}}
                        <div class="tab-pane fade show active p-3" id="nav-roles" role="tabpanel" aria-labelledby="nav-roles-tab">
                            <div class="form-group">
                                <label class="form-label required">Rôle principal</label>
                                <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required> {{-- form-select for BS5 --}}
                                    <option value="">Sélectionnez un rôle</option>
                                    @foreach($roles as $role) {{-- Assuming $roles is passed from controller --}}
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group mt-3">
                                <label class="form-label">Accès aux modules (Exemple)</label>
                                <div class="row">
                                    @foreach($modules as $moduleKey => $moduleName) {{-- Assuming $modules is an array like ['sales' => 'Ventes'] or just ['sales', 'inventory'] --}}
                                        <div class="col-md-4">
                                            <div class="form-check form-check-inline card card-body p-2 mb-2"> {{-- Added mb-2 for spacing --}}
                                                <input type="checkbox" name="module_access[]" value="{{ is_string($moduleKey) ? $moduleKey : $moduleName }}" class="form-check-input" id="module-{{ is_string($moduleKey) ? $moduleKey : $moduleName }}">
                                                <label class="form-check-label" for="module-{{ is_string($moduleKey) ? $moduleKey : $moduleName }}">
                                                    {{ ucfirst($moduleName) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('module_access') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Onglet Préférences --}}
                        <div class="tab-pane fade p-3" id="nav-preferences" role="tabpanel" aria-labelledby="nav-preferences-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Langue par défaut</label>
                                        <select name="locale" class="form-select @error('locale') is-invalid @enderror">
                                             @foreach(config('app.available_locales', ['fr' => 'Français', 'en' => 'English']) as $locale_code => $locale_name)
                                                <option value="{{ $locale_code }}" {{ old('locale', config('app.locale')) == $locale_code ? 'selected' : '' }}>
                                                    {{ $locale_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('locale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                     <div class="form-group mt-3">
                                        <label class="form-label">Préférences utilisateur (JSON)</label>
                                        <textarea name="preferences" class="form-control @error('preferences') is-invalid @enderror" rows="2" placeholder='{"theme": "clair", "items_par_page": 50}'>{{ old('preferences') }}</textarea>
                                        @error('preferences') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Devise par défaut</label>
                                        <select name="currency" class="form-select @error('currency') is-invalid @enderror">
                                            @foreach(config('app.supported_currencies', ['XOF' => ['name' => 'Franc CFA', 'symbol' => 'FCFA']]) as $currency_code => $currency_details)
                                                <option value="{{ $currency_code }}" {{ old('currency', 'XOF') == $currency_code ? 'selected' : '' }}>
                                                    {{ $currency_details['symbol'] }} - {{ $currency_details['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="form-group mt-3">
                                        <label class="form-label d-block">Options</label>
                                        <div class="form-check form-switch d-block mb-2">
                                            <input type="checkbox" name="status" id="status_aide" class="form-check-input" value="1" {{ old('status', 1) ? 'checked' : '' }}>
                                            <label for="status_aide" class="form-check-label">Compte actif</label>
                                        </div>
                                        <div class="form-check form-switch d-block mb-2">
                                            <input type="checkbox" name="is_admin" id="is_admin_aide" class="form-check-input" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                                            <label for="is_admin_aide" class="form-check-label">Accès administrateur</label>
                                        </div>
                                        <div class="form-check form-switch d-block mb-2">
                                            <input type="checkbox" name="notifications_enabled" id="notifications_enabled_aide" class="form-check-input" value="1" {{ old('notifications_enabled', 1) ? 'checked' : '' }}>
                                            <label for="notifications_enabled_aide" class="form-check-label">Activer les notifications</label>
                                        </div>
                                        <div class="form-check form-switch d-block">
                                            <input type="checkbox" name="two_factor_enabled" id="two_factor_enabled_aide" class="form-check-input" value="1" {{ old('two_factor_enabled') ? 'checked' : '' }}>
                                            <label for="two_factor_enabled_aide" class="form-check-label">Authentification à deux facteurs</label>
                                        </div>
                                        @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        @error('is_admin') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card-action mt-4">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fa fa-save me-2"></i>Enregistrer l'Utilisateur
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-danger btn-lg ms-2">
                    <i class="fa fa-times me-2"></i>Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Validation dynamique du mot de passe (exemple simple)
    const passwordInputAide = document.getElementById('password_aide');
    if (passwordInputAide) {
        passwordInputAide.addEventListener('input', function(e) {
            const value = e.target.value;
            // Basic checks, you can enhance this
            if (value.length < 8) {
                e.target.setCustomValidity('Le mot de passe doit contenir au moins 8 caractères.');
            } else {
                e.target.setCustomValidity('');
            }
            // Add more checks for uppercase, number, special char if needed
            // And provide visual feedback
        });
    }
</script>
@endpush
