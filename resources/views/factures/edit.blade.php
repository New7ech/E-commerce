@extends('layouts.app')

@section('title', 'Modifier la Facture')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Modifier la Facture</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('welcome') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('factures.index') }}">Factures</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Modifier #{{ $facture->numero ?? $facture->id }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Modifier la Facture #{{ $facture->numero ?? $facture->id }}</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('factures.update', $facture->id) }}" method="POST" id="factureForm">
                    @csrf
                    @method('PUT')

                    <h5 class="mb-3">Informations Client</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="client_nom">Nom du client <span class="text-danger">*</span></label>
                                <input type="text" name="client_nom" id="client_nom" class="form-control @error('client_nom') is-invalid @enderror" required value="{{ old('client_nom', $facture->client_nom) }}">
                                @error('client_nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="client_prenom">Prénom du client</label>
                                <input type="text" name="client_prenom" id="client_prenom" class="form-control @error('client_prenom') is-invalid @enderror" value="{{ old('client_prenom', $facture->client_prenom) }}">
                                @error('client_prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="client_telephone">Téléphone</label>
                                <input type="text" name="client_telephone" id="client_telephone" class="form-control @error('client_telephone') is-invalid @enderror" value="{{ old('client_telephone', $facture->client_telephone) }}">
                                @error('client_telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                         <div class="col-md-8">
                            <div class="form-group">
                                <label for="client_adresse">Adresse</label>
                                <input type="text" name="client_adresse" id="client_adresse" class="form-control @error('client_adresse') is-invalid @enderror" value="{{ old('client_adresse', $facture->client_adresse) }}">
                                @error('client_adresse') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="client_email">Email</label>
                                <input type="email" name="client_email" id="client_email" class="form-control @error('client_email') is-invalid @enderror" value="{{ old('client_email', $facture->client_email) }}">
                                @error('client_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mt-4 mb-3">Articles de la Facture</h5>
                    <div id="articles-container">
                        @foreach($facture->articles as $idx => $item)
                        <div class="row g-3 mb-3 align-items-center article-row">
                            <div class="col-md-5">
                                <label class="form-label">Article {{ $idx + 1 }}</label>
                                <select name="articles[{{ $idx }}][article_id]" class="form-control article-select form-select" required>
                                    <option value="">Sélectionnez un article</option>
                                    @foreach($articles as $article)
                                        <option value="{{ $article->id }}" data-prix="{{ $article->prix }}" data-stock="{{ $article->quantite_stock ?? $article->quantite }}" {{ $item->id == $article->id ? 'selected' : '' }}>
                                            {{ $article->name }} ({{ number_format($article->prix, 0, ',', ' ') }} FCFA) - Stock: {{ $article->quantite_stock ?? $article->quantite }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantité</label>
                                <input type="number" name="articles[{{ $idx }}][quantity]" class="form-control quantity-input" min="1" required value="{{ $item->pivot->quantite }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Prix Total Article</label>
                                <input type="text" class="form-control article-total-price" readonly value="{{ number_format($item->pivot->quantite * $item->pivot->prix_unitaire, 0, ',', ' ') }} FCFA">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm remove-article"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-success btn-sm" id="addArticleBtn">
                            <i class="fa fa-plus"></i> Ajouter un Article
                        </button>
                    </div>

                    <hr>
                    <h5 class="mt-4 mb-3">Récapitulatif</h5>
                     <div class="row bg-light p-3 rounded mb-4">
                        <div class="col-md-4">
                            <p class="mb-1">Montant HT :</p>
                            <h4 class="fw-bold mb-0"><span id="montantHT">{{ number_format($facture->montant_ht, 0, ',', ' ') }}</span> FCFA</h4>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1">TVA ({{ $facture->tva ?? 18 }}%) :</p>
                             <h4 class="fw-bold mb-0"><span id="montantTVA">{{ number_format($facture->montant_ht * (($facture->tva ?? 18) / 100), 0, ',', ' ') }}</span> FCFA</h4>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1">Montant TTC :</p>
                             <h4 class="fw-bold mb-0"><span id="montantTTC">{{ number_format($facture->montant_ttc, 0, ',', ' ') }}</span> FCFA</h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mode_paiement">Mode de paiement</label>
                                <select name="mode_paiement" id="mode_paiement" class="form-control @error('mode_paiement') is-invalid @enderror form-select">
                                    <option value="">Sélectionnez un mode de paiement</option>
                                    <option value="carte" {{ old('mode_paiement', $facture->mode_paiement) == 'carte' ? 'selected' : '' }}>Carte</option>
                                    <option value="chèque" {{ old('mode_paiement', $facture->mode_paiement) == 'chèque' ? 'selected' : '' }}>Chèque</option>
                                    <option value="espèces" {{ old('mode_paiement', $facture->mode_paiement) == 'espèces' ? 'selected' : '' }}>Espèces</option>
                                    <option value="virement" {{ old('mode_paiement', $facture->mode_paiement) == 'virement' ? 'selected' : '' }}>Virement</option>
                                    <option value="autre" {{ old('mode_paiement', $facture->mode_paiement) == 'autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('mode_paiement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="statut_paiement">Statut du paiement <span class="text-danger">*</span></label>
                                <select name="statut_paiement" id="statut_paiement" class="form-control @error('statut_paiement') is-invalid @enderror form-select" required>
                                    <option value="impayé" {{ old('statut_paiement', $facture->statut_paiement) == 'impayé' ? 'selected' : '' }}>Impayé</option>
                                    <option value="partiel" {{ old('statut_paiement', $facture->statut_paiement) == 'partiel' ? 'selected' : '' }}>Partiel</option>
                                    <option value="payé" {{ old('statut_paiement', $facture->statut_paiement) == 'payé' ? 'selected' : '' }}>Payé</option>
                                </select>
                                @error('statut_paiement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                     <div class="form-group">
                        <label for="date_facture">Date de facturation <span class="text-danger">*</span></label>
                        <input type="date" name="date_facture" id="date_facture" class="form-control @error('date_facture') is-invalid @enderror" value="{{ old('date_facture', $facture->date_facture ? \Carbon\Carbon::parse($facture->date_facture)->format('Y-m-d') : date('Y-m-d')) }}" required>
                        @error('date_facture') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="card-action">
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="fa fa-save"></i> Enregistrer les Modifications
                        </button>
                        <a href="{{ route('factures.index') }}" class="btn btn-danger">
                            <i class="fa fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    let articleIndex = {{ count($facture->articles) }}; // Initialize with current number of articles
    const articlesContainer = document.getElementById('articles-container');
    const addArticleButton = document.getElementById('addArticleBtn');
    const submitButton = document.getElementById('submitBtn');
    const articlesData = @json($articles); // Assuming $articles is passed from controller for new rows

    function createSelectElement(index) {
        const select = document.createElement('select');
        select.name = `articles[${index}][article_id]`;
        select.className = 'form-control article-select form-select';
        select.required = true;
        let optionsHtml = '<option value="">Sélectionnez un article</option>';
        articlesData.forEach(article => {
            optionsHtml += `<option value="${article.id}" data-prix="${article.prix}" data-stock="${article.quantite_stock || article.quantite}">
                                ${article.name} (${parseFloat(article.prix).toFixed(0)} FCFA) - Stock: ${article.quantite_stock || article.quantite}
                           </option>`;
        });
        select.innerHTML = optionsHtml;
        return select;
    }

    function addArticleRow() {
        const newRow = document.createElement('div');
        newRow.className = 'row g-3 mb-3 align-items-center article-row';

        const selectCol = document.createElement('div');
        selectCol.className = 'col-md-5';
        const selectLabel = document.createElement('label');
        selectLabel.className = 'form-label';
        selectLabel.textContent = `Article ${articlesContainer.children.length + 1}`; // Dynamic numbering
        selectCol.appendChild(selectLabel);
        selectCol.appendChild(createSelectElement(articleIndex)); // Use current articleIndex for unique name

        const quantityCol = document.createElement('div');
        quantityCol.className = 'col-md-3';
        const quantityLabel = document.createElement('label');
        quantityLabel.className = 'form-label';
        quantityLabel.textContent = 'Quantité';
        quantityCol.appendChild(quantityLabel);
        const quantityInput = document.createElement('input');
        quantityInput.type = 'number';
        quantityInput.name = `articles[${articleIndex}][quantity]`; // Use current articleIndex
        quantityInput.className = 'form-control quantity-input';
        quantityInput.min = '1';
        quantityInput.required = true;
        quantityCol.appendChild(quantityInput);

        const priceCol = document.createElement('div');
        priceCol.className = 'col-md-3';
        const priceLabel = document.createElement('label');
        priceLabel.className = 'form-label';
        priceLabel.textContent = 'Prix Total Article';
        priceCol.appendChild(priceLabel);
        const priceDisplay = document.createElement('input');
        priceDisplay.type = 'text';
        priceDisplay.className = 'form-control article-total-price';
        priceDisplay.readOnly = true;
        priceCol.appendChild(priceDisplay);

        const removeCol = document.createElement('div');
        removeCol.className = 'col-md-1 d-flex align-items-end';
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-danger btn-sm remove-article';
        removeButton.innerHTML = '<i class="fa fa-trash"></i>';
        removeCol.appendChild(removeButton);

        newRow.appendChild(selectCol);
        newRow.appendChild(quantityCol);
        newRow.appendChild(priceCol);
        newRow.appendChild(removeCol);
        articlesContainer.appendChild(newRow);

        articleIndex++; // Increment index for next new row
        bindRowEvents(newRow);
        updateSubmitButtonState();
    }

    function calculateRowTotal(row) {
        const select = row.querySelector('.article-select');
        const quantityInput = row.querySelector('.quantity-input');
        const priceDisplay = row.querySelector('.article-total-price');

        const selectedOption = select.options[select.selectedIndex];
        const unitPrice = parseFloat(selectedOption.dataset.prix || 0);
        const quantity = parseInt(quantityInput.value || 0);

        priceDisplay.value = (unitPrice * quantity).toFixed(0) + ' FCFA';
    }

    function calculateGrandTotals() {
        let totalHT = 0;
        document.querySelectorAll('.article-row').forEach(row => {
            const select = row.querySelector('.article-select');
            const qtyInput = row.querySelector('.quantity-input');
            const prix = parseFloat(select.selectedOptions[0]?.dataset.prix || 0); // Price from selected option
            const stock = parseInt(select.selectedOptions[0]?.dataset.stock || 0);
            const qty = parseInt(qtyInput.value || 0);

            if (qty > 0 && stock > 0 && qty > stock) {
                qtyInput.classList.add('is-invalid');
                qtyInput.setCustomValidity(`Stock insuffisant (disponible: ${stock})`);
                qtyInput.reportValidity();
            } else if (qty > 0 && stock === 0 && select.value !== "") { // Check if an article is selected
                 qtyInput.classList.add('is-invalid');
                qtyInput.setCustomValidity(`Article hors stock`);
                qtyInput.reportValidity();
            } else {
                qtyInput.classList.remove('is-invalid');
                qtyInput.setCustomValidity('');
            }
            totalHT += prix * qty;
        });

        const currentTVApercent = parseFloat({{ $facture->tva ?? 18 }}) / 100;
        const tva = totalHT * currentTVApercent;
        const ttc = totalHT + tva;

        document.getElementById('montantHT').textContent = totalHT.toFixed(0);
        document.getElementById('montantTVA').textContent = tva.toFixed(0);
        document.getElementById('montantTTC').textContent = ttc.toFixed(0);
        updateSubmitButtonState();
    }

    function updateSubmitButtonState() {
        let allValid = true;
        let hasArticles = false;
        document.querySelectorAll('.article-row').forEach(row => {
            hasArticles = true;
            const qtyInput = row.querySelector('.quantity-input');
            if (qtyInput.classList.contains('is-invalid') || !qtyInput.value || parseInt(qtyInput.value) <= 0) {
                allValid = false;
            }
            const select = row.querySelector('.article-select');
            if (!select.value) {
                allValid = false;
            }
        });
        const clientNom = document.getElementById('client_nom').value;
        submitButton.disabled = !allValid || !hasArticles || !clientNom;
    }

    function bindRowEvents(row) {
        const select = row.querySelector('.article-select');
        const quantityInput = row.querySelector('.quantity-input');

        select.addEventListener('change', () => {
            calculateRowTotal(row);
            calculateGrandTotals();
        });
        quantityInput.addEventListener('input', () => {
            calculateRowTotal(row);
            calculateGrandTotals();
        });

        row.querySelector('.remove-article').addEventListener('click', (e) => {
            e.target.closest('.article-row').remove();
            calculateGrandTotals();
            // Renumber labels after removal
            let currentIdx = 1;
            articlesContainer.querySelectorAll('.article-row').forEach(r => {
                r.querySelector('label.form-label').textContent = `Article ${currentIdx++}`;
            });
        });
    }

    addArticleButton.addEventListener('click', addArticleRow);

    // Bind events to initially loaded rows
    document.querySelectorAll('.article-row').forEach(row => {
        bindRowEvents(row);
        // calculateRowTotal(row); // Already done by Blade for existing items
    });
    calculateGrandTotals(); // Initial calculation for existing items
    document.getElementById('client_nom').addEventListener('input', updateSubmitButtonState);

});
</script>
@endpush
