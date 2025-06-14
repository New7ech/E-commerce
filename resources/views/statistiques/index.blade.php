@extends('layouts.app')

@section('contenus')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title mb-0">Statistiques Générales</h1>
        </div>
        <div class="card-body">
            <!-- Summary Cards Row -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Articles en Stock</h5>
                            <p class="card-text fs-2 fw-bold">{{ $totalArticlesInStock }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Revenu Factures (30j)</h5>
                            <p class="card-text fs-2 fw-bold">{{ number_format($totalSalesRevenueLast30Days, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                </div>
                 <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Revenu E-commerce (30j)</h5>
                            <p class="card-text fs-2 fw-bold">{{ number_format($totalEcommerceRevenueLast30Days, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                     <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Commandes E-commerce (30j)</h5>
                            <p class="card-text fs-2 fw-bold">{{ $totalEcommerceOrdersLast30Days }}</p>
                        </div>
                    </div>
                </div>
                {{-- Placeholder for another summary card if needed --}}
            </div>


            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Articles par Catégorie</div>
                        <div class="card-body">
                            <div style="position: relative; height:350px; width:100%;">
                                <canvas id="articlesPerCategoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Tendances des Ventes (Factures, 30j)</div>
                        <div class="card-body">
                            <div style="position: relative; height:350px; width:100%;">
                                <canvas id="salesTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">Tendances des Ventes E-commerce (Commandes, 30j)</div>
                        <div class="card-body">
                            <div style="position: relative; height:350px; width:100%;">
                                <canvas id="ecommerceSalesTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                 <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Top 5 Articles Vendus (Factures, 30j)</div>
                        <div class="card-body">
                            <div style="position: relative; height:350px; width:100%;">
                                <canvas id="bestSellingArticlesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Top 5 Produits E-commerce (Commandes, 30j)</div>
                        <div class="card-body">
                            <div style="position: relative; height:350px; width:100%;">
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
                        <div class="card-header">Articles à Stock Faible (Moins de 10 unités)</div>
                        <div class="card-body">
                            @if($lowStockArticles->isEmpty())
                                <p class="text-center text-muted">Aucun article à stock faible pour le moment.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-light">
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
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Helper function to generate diverse colors
    function generateChartColors(numColors) {
        const baseColors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
            '#E7E9ED', '#707070', '#FFD700', '#ADFF2F', '#00FFFF', '#FF00FF'
        ];
        let colors = [];
        for (let i = 0; i < numColors; i++) {
            colors.push(baseColors[i % baseColors.length]);
        }
        return colors;
    }

    // Articles per Category (Pie Chart)
    const articlesPerCategoryLabels = @json($articlesPerCategoryLabels);
    const articlesPerCategoryData = @json($articlesPerCategoryData);
    const articlesPerCategoryCanvas = document.getElementById('articlesPerCategoryChart');
    if (articlesPerCategoryCanvas && articlesPerCategoryLabels.length > 0 && articlesPerCategoryData.length > 0) {
        const ctxPie = articlesPerCategoryCanvas.getContext('2d');
        new Chart(ctxPie, {
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
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    } else if (articlesPerCategoryCanvas) {
        const ctx = articlesPerCategoryCanvas.getContext('2d');
        if (ctx) ctx.fillText("Aucune donnée disponible pour les catégories d'articles.", 10, 50);
    }

    // Sales Trend (Facture-based Line Chart)
    const salesTrendLabels = @json($salesTrendLabels); // Facture-based
    const salesTrendData = @json($salesTrendData);     // Facture-based
    const salesTrendCanvas = document.getElementById('salesTrendChart');
    if (salesTrendCanvas && salesTrendLabels.length > 0 && salesTrendData.length > 0) {
        const ctxLineFacture = salesTrendCanvas.getContext('2d');
        new Chart(ctxLineFacture, {
            type: 'line',
            data: {
                labels: salesTrendLabels,
                datasets: [{
                    label: 'Ventes Journalières (Factures FCFA)',
                    data: salesTrendData,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return value + ' FCFA'; }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    } else if (salesTrendCanvas) {
        const ctx = salesTrendCanvas.getContext('2d');
        if (ctx) ctx.fillText("Aucune donnée disponible pour les tendances de ventes (Factures).", 10, 50);
    }

     // E-commerce Sales Trend (Line Chart) - Added
    const ecommerceSalesTrendLabels = @json($ecommerceSalesTrendLabels);
    const ecommerceSalesTrendData = @json($ecommerceSalesTrendData);
    const ecommerceSalesTrendCanvas = document.getElementById('ecommerceSalesTrendChart');
    if (ecommerceSalesTrendCanvas && ecommerceSalesTrendLabels.length > 0 && ecommerceSalesTrendData.length > 0) {
        const ctxLineEcom = ecommerceSalesTrendCanvas.getContext('2d');
        new Chart(ctxLineEcom, {
            type: 'line',
            data: {
                labels: ecommerceSalesTrendLabels,
                datasets: [{
                    label: 'Ventes E-commerce Journalières (FCFA)',
                    data: ecommerceSalesTrendData,
                    borderColor: 'rgb(255, 99, 132)', // Different color
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return value + ' FCFA'; }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    } else if (ecommerceSalesTrendCanvas) {
        const ctx = ecommerceSalesTrendCanvas.getContext('2d');
        if (ctx) ctx.fillText("Aucune donnée disponible pour les tendances de ventes E-commerce.", 10, 50);
    }


    // Best Selling Articles (Facture-based Bar Chart)
    const bestSellingArticlesLabels = @json($bestSellingArticlesLabels); // Facture-based
    const bestSellingArticlesData = @json($bestSellingArticlesData);     // Facture-based
    const bestSellingArticlesCanvas = document.getElementById('bestSellingArticlesChart');
    if (bestSellingArticlesCanvas && bestSellingArticlesLabels.length > 0 && bestSellingArticlesData.length > 0) {
        const ctxBarFacture = bestSellingArticlesCanvas.getContext('2d');
        new Chart(ctxBarFacture, {
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
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    } else if (bestSellingArticlesCanvas) {
        const ctx = bestSellingArticlesCanvas.getContext('2d');
        if (ctx) ctx.fillText("Aucune donnée disponible pour les meilleurs articles vendus (Factures).", 10, 50);
    }

    // Best Selling E-commerce Products (Bar Chart) - Added
    const bestSellingEcommerceProductsLabels = @json($bestSellingEcommerceProductsLabels);
    const bestSellingEcommerceProductsData = @json($bestSellingEcommerceProductsData);
    const bestSellingEcommerceProductsCanvas = document.getElementById('bestSellingEcommerceProductsChart');
    if (bestSellingEcommerceProductsCanvas && bestSellingEcommerceProductsLabels.length > 0 && bestSellingEcommerceProductsData.length > 0) {
        const ctxBarEcom = bestSellingEcommerceProductsCanvas.getContext('2d');
        new Chart(ctxBarEcom, {
            type: 'bar',
            data: {
                labels: bestSellingEcommerceProductsLabels,
                datasets: [{
                    label: 'Quantité Vendue (E-commerce)',
                    data: bestSellingEcommerceProductsData,
                    backgroundColor: generateChartColors(bestSellingEcommerceProductsLabels.length).map(color => color.replace('1)', '0.7)')), // Slightly different colors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    } else if (bestSellingEcommerceProductsCanvas) {
        const ctx = bestSellingEcommerceProductsCanvas.getContext('2d');
        if (ctx) ctx.fillText("Aucune donnée disponible pour les meilleurs produits E-commerce.", 10, 50);
    }

});
</script>
@endsection
