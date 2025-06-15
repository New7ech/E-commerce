@extends('layouts.app')

@section('title', 'Statistiques des Ventes (Garde)') {{-- Clarified title --}}

@section('content') {{-- Changed from contenus to content --}}
<div class="page-header">
    <h3 class="fw-bold mb-3">Statistiques des Ventes (Garde)</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Statistiques Garde</li>
    </ul>
</div>

<div class="row">
    <!-- Diagramme circulaire des statuts de paiement -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Répartition des statuts de paiement</h4>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:300px; width:100%;">
                    <canvas id="doughnutChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique à barres du CA mensuel -->
    <div class="col-md-6"> {{-- Changed to col-md-6 to allow side-by-side with line chart if desired, or keep as col-md-12 for full width --}}
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Chiffre d'affaires mensuel TTC</h4>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:300px; width:100%;">
                    <canvas id="multipleBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <!-- Courbe d'évolution du CA -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Évolution du chiffre d'affaires</h4>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:350px; width:100%;">
                    <canvas id="lineChartEvolutionCA"></canvas> {{-- Renamed to avoid conflict if another lineChart id exists --}}
                </div>
                {{-- <div id="myChartLegend"></div> --}} {{-- Custom legend div removed, using Chart.js built-in legend --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Chart.js is assumed to be loaded globally from layouts.app.blade.php --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Donut Chart (Statuts de paiement)
    const paymentStatusData = @json($paymentStatus ?? []);
    if (document.getElementById('doughnutChart') && paymentStatusData.length > 0) {
        const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
        new Chart(doughnutCtx, {
            type: 'doughnut',
            data: {
                labels: paymentStatusData.map(item => item.statut_paiement),
                datasets: [{
                    data: paymentStatusData.map(item => item.count),
                    backgroundColor: ['#59d05d', '#f3545d', '#fdaf4b', '#177dff', '#67c5e0'] // Added more colors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    } else if (document.getElementById('doughnutChart')) {
        document.getElementById('doughnutChart').parentElement.innerHTML = '<p class="text-center text-muted p-5">Aucune donnée pour les statuts de paiement.</p>';
    }

    // Bar Chart (CA mensuel)
    const salesData = @json($sales ?? []);
    if (document.getElementById('multipleBarChart') && salesData.length > 0) {
        const barCtx = document.getElementById('multipleBarChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: salesData.map(item => item.mois),
                datasets: [{
                    label: "Chiffre d'affaires TTC",
                    data: salesData.map(item => item.total_ttc),
                    backgroundColor: '#177dff',
                    borderColor: '#177dff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { callback: function(value) { return value.toLocaleString() + ' FCFA'; } } }
                }
            }
        });
    } else if (document.getElementById('multipleBarChart')) {
         document.getElementById('multipleBarChart').parentElement.innerHTML = '<p class="text-center text-muted p-5">Aucune donnée pour le chiffre d\'affaires mensuel.</p>';
    }

    // Line Chart (Évolution CA) - Assuming same $sales data for this example
    if (document.getElementById('lineChartEvolutionCA') && salesData.length > 0) {
        const lineCtx = document.getElementById('lineChartEvolutionCA').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: salesData.map(item => item.mois),
                datasets: [{
                    label: "CA TTC",
                    data: salesData.map(item => item.total_ttc),
                    borderColor: '#177dff', // Kaiadmin primary color
                    backgroundColor: 'rgba(23, 125, 255, 0.2)', // Lighter version for area fill
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: function(value) { return value.toLocaleString() + ' FCFA'; } } }
                }
            }
        });
    } else if (document.getElementById('lineChartEvolutionCA')) {
        document.getElementById('lineChartEvolutionCA').parentElement.innerHTML = '<p class="text-center text-muted p-5">Aucune donnée pour l\'évolution du chiffre d\'affaires.</p>';
    }
});
</script>
@endpush
