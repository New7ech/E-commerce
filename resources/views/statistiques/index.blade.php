@extends('layouts.app')

@section('title', 'Statistiques')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Statistiques Générales</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Statistiques</li>
    </ul>
</div>

{{-- Main content from original file, wrapped in Kaiadmin structure --}}
<div class="row">
    {{-- Summary Cards Row 1 --}}
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-box-open"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Articles en Stock</p>
                            <h4 class="card-title">{{ $totalArticlesInStock ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Revenu Factures (30j)</p>
                            <h4 class="card-title">{{ number_format($totalSalesRevenueLast30Days ?? 0, 0, ',', ' ') }} FCFA</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Revenu E-commerce (30j)</p>
                            <h4 class="card-title">{{ number_format($totalEcommerceRevenueLast30Days ?? 0, 0, ',', ' ') }} FCFA</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Commandes E-com (30j)</p>
                            <h4 class="card-title">{{ $totalEcommerceOrdersLast30Days ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Charts Row 1 -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Articles par Catégorie</h4>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:350px; width:100%;">
                    <canvas id="articlesPerCategoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Tendances des Ventes (Factures, 30j)</h4>
            </div>
            <div class="card-body">
                 <div class="chart-container" style="position: relative; height:350px; width:100%;">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                 <h4 class="card-title">Tendances des Ventes E-commerce (Commandes, 30j)</h4>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:350px; width:100%;">
                    <canvas id="ecommerceSalesTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 3 -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Top 5 Articles Vendus (Factures, 30j)</h4>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:350px; width:100%;">
                    <canvas id="bestSellingArticlesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Top 5 Produits E-commerce (Commandes, 30j)</h4>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:350px; width:100%;">
                    <canvas id="bestSellingEcommerceProductsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Table Row -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Articles à Stock Faible (Moins de 10 unités)</h4>
            </div>
            <div class="card-body">
                @if($lowStockArticles->isEmpty())
                    <p class="text-center text-muted">Aucun article à stock faible pour le moment.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light"> {{-- Or simply thead-light for BS5 --}}
                                <tr>
                                    <th>Nom de l'article</th>
                                    <th class="text-end">Quantité restante</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockArticles as $article)
                                <tr>
                                    <td>{{ $article->name }}</td>
                                    <td class="text-end fw-bold {{ $article->quantite < 5 ? 'text-danger' : '' }}">{{ $article->quantite }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection {{-- End of content section --}}

@push('scripts')
{{-- Chart.js is assumed to be loaded globally from layouts.app.blade.php --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Helper function to generate diverse colors for charts
    function generateChartColors(numColors) {
        const baseColors = [
            'rgba(255, 99, 132, 0.8)', 'rgba(54, 162, 235, 0.8)', 'rgba(255, 206, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)', 'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)',
            'rgba(199, 199, 199, 0.8)', 'rgba(83, 102, 83, 0.8)', 'rgba(255, 218, 185, 0.8)',
            'rgba(173, 255, 47, 0.8)', 'rgba(0, 255, 255, 0.8)', 'rgba(255, 0, 255, 0.8)'
        ];
        let colors = [];
        for (let i = 0; i < numColors; i++) {
            colors.push(baseColors[i % baseColors.length]);
        }
        return colors;
    }

    // Articles per Category (Pie Chart)
    const articlesPerCategoryLabels = @json($articlesPerCategoryLabels ?? []);
    const articlesPerCategoryData = @json($articlesPerCategoryData ?? []);
    const articlesPerCategoryCanvas = document.getElementById('articlesPerCategoryChart');
    if (articlesPerCategoryCanvas && articlesPerCategoryLabels.length > 0 && articlesPerCategoryData.length > 0) {
        new Chart(articlesPerCategoryCanvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels: articlesPerCategoryLabels,
                datasets: [{
                    label: 'Articles par Catégorie',
                    data: articlesPerCategoryData,
                    backgroundColor: generateChartColors(articlesPerCategoryLabels.length),
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } }
            }
        });
    } else if (articlesPerCategoryCanvas) {
        const ctx = articlesPerCategoryCanvas.getContext('2d');
        if (ctx) { articlesPerCategoryCanvas.parentElement.innerHTML = '<p class="text-center text-muted p-5">Aucune donnée pour les catégories d\'articles.</p>'; }
    }

    // Sales Trend (Facture-based Line Chart)
    const salesTrendLabels = @json($salesTrendLabels ?? []);
    const salesTrendData = @json($salesTrendData ?? []);
    const salesTrendCanvas = document.getElementById('salesTrendChart');
    if (salesTrendCanvas && salesTrendLabels.length > 0 && salesTrendData.length > 0) {
        new Chart(salesTrendCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: salesTrendLabels,
                datasets: [{
                    label: 'Ventes Journalières (Factures FCFA)',
                    data: salesTrendData,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true, tension: 0.1
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return value + ' FCFA'; } } } },
                plugins: { tooltip: { callbacks: { label: function(context) { return (context.dataset.label || '') + ': ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA'; } } } }
            }
        });
    } else if (salesTrendCanvas) {
         const ctx = salesTrendCanvas.getContext('2d');
        if (ctx) { salesTrendCanvas.parentElement.innerHTML = '<p class="text-center text-muted p-5">Aucune donnée pour les tendances de ventes (Factures).</p>'; }
    }

    // E-commerce Sales Trend (Line Chart)
    const ecommerceSalesTrendLabels = @json($ecommerceSalesTrendLabels ?? []);
    const ecommerceSalesTrendData = @json($ecommerceSalesTrendData ?? []);
    const ecommerceSalesTrendCanvas = document.getElementById('ecommerceSalesTrendChart');
    if (ecommerceSalesTrendCanvas && ecommerceSalesTrendLabels.length > 0 && ecommerceSalesTrendData.length > 0) {
        new Chart(ecommerceSalesTrendCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: ecommerceSalesTrendLabels,
                datasets: [{
                    label: 'Ventes E-commerce Journalières (FCFA)',
                    data: ecommerceSalesTrendData,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true, tension: 0.1
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return value + ' FCFA'; } } } },
                plugins: { tooltip: { callbacks: { label: function(context) { return (context.dataset.label || '') + ': ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA'; } } } }
            }
        });
    } else if (ecommerceSalesTrendCanvas) {
        const ctx = ecommerceSalesTrendCanvas.getContext('2d');
        if (ctx) { ecommerceSalesTrendCanvas.parentElement.innerHTML = '<p class="text-center text-muted p-5">Aucune donnée pour les tendances de ventes E-commerce.</p>'; }
    }

    // Best Selling Articles (Facture-based Bar Chart)
    const bestSellingArticlesLabels = @json($bestSellingArticlesLabels ?? []);
    const bestSellingArticlesData = @json($bestSellingArticlesData ?? []);
    const bestSellingArticlesCanvas = document.getElementById('bestSellingArticlesChart');
    if (bestSellingArticlesCanvas && bestSellingArticlesLabels.length > 0 && bestSellingArticlesData.length > 0) {
        new Chart(bestSellingArticlesCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: bestSellingArticlesLabels,
                datasets: [{
                    label: 'Quantité Vendue (Factures)',
                    data: bestSellingArticlesData,
                    backgroundColor: generateChartColors(bestSellingArticlesLabels.length),
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    } else if (bestSellingArticlesCanvas) {
        const ctx = bestSellingArticlesCanvas.getContext('2d');
        if (ctx) { bestSellingArticlesCanvas.parentElement.innerHTML = '<p class="text-center text-muted p-5">Aucune donnée pour les meilleurs articles (Factures).</p>'; }
    }

    // Best Selling E-commerce Products (Bar Chart)
    const bestSellingEcommerceProductsLabels = @json($bestSellingEcommerceProductsLabels ?? []);
    const bestSellingEcommerceProductsData = @json($bestSellingEcommerceProductsData ?? []);
    const bestSellingEcommerceProductsCanvas = document.getElementById('bestSellingEcommerceProductsChart');
    if (bestSellingEcommerceProductsCanvas && bestSellingEcommerceProductsLabels.length > 0 && bestSellingEcommerceProductsData.length > 0) {
        new Chart(bestSellingEcommerceProductsCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: bestSellingEcommerceProductsLabels,
                datasets: [{
                    label: 'Quantité Vendue (E-commerce)',
                    data: bestSellingEcommerceProductsData,
                    backgroundColor: generateChartColors(bestSellingEcommerceProductsLabels.length).map(color => color.replace('0.8)', '0.6)')), // Slightly different opacity
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    } else if (bestSellingEcommerceProductsCanvas) {
        const ctx = bestSellingEcommerceProductsCanvas.getContext('2d');
        if (ctx) { bestSellingEcommerceProductsCanvas.parentElement.innerHTML = '<p class="text-center text-muted p-5">Aucune donnée pour les meilleurs produits (E-commerce).</p>'; }
    }
});
</script>
@endpush
