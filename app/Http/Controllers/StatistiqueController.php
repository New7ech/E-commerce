<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use App\Models\Facture;
use App\Models\Order; // Added
use App\Models\OrderItem; // Added
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Contrôleur pour afficher diverses statistiques liées à l'inventaire,
 * aux ventes par factures et aux commandes e-commerce.
 */
class StatistiqueController extends Controller
{
    /**
     * Prépare et affiche la page des statistiques.
     * Calcule diverses métriques telles que le stock total, les articles par catégorie,
     * les articles à faible stock, les revenus des ventes (basées sur les factures et les commandes e-commerce),
     * les tendances des ventes et les articles les plus vendus.
     *
     * @return \Illuminate\View\View La vue affichant les statistiques.
     */
    public function index(): \Illuminate\View\View
    {
        // --- Statistiques Existantes (principalement inventaire et ventes basées sur Factures) ---
        // 1. Nombre total d'articles en stock
        $totalArticlesInStock = Article::sum('quantite');

        // 2. Nombre d'articles par catégorie
        $categories = Categorie::withCount('articles')->get(); // Utilise withCount pour optimiser.
        $articlesPerCategoryLabels = $categories->pluck('name')->toArray();
        $articlesPerCategoryData = $categories->pluck('articles_count')->toArray();

        // 3. Articles avec un stock faible
        $lowStockThreshold = 10; // Seuil pour stock faible.
        $lowStockArticles = Article::where('quantite', '<', $lowStockThreshold)
                                   ->select('name', 'quantite') // Sélectionne uniquement les colonnes nécessaires.
                                   ->orderBy('quantite', 'asc')
                                   ->get();

        // 4. Revenu total des ventes des 30 derniers jours (basé sur Factures)
        $totalSalesRevenueLast30Days = Facture::where('date_facture', '>=', Carbon::now()->subDays(30))
                                              ->sum('montant_ttc');

        // 5. Tendance des ventes des 30 derniers jours (basé sur Factures)
        $salesTrendRaw = Facture::select(
                DB::raw('DATE(date_facture) as date'), // Extrait la date.
                DB::raw('SUM(montant_ttc) as total_sales') // Calcule la somme des ventes.
            )
            ->where('date_facture', '>=', Carbon::now()->subDays(30))
            ->groupBy('date') // Regroupe par date.
            ->orderBy('date', 'asc')
            ->get();

        $salesTrendLabels = [];
        $salesTrendData = [];
        // Initialise avec toutes les dates des 30 derniers jours pour assurer la continuité du graphique.
        $period = Carbon::now()->subDays(29); // Commence il y a 29 jours pour inclure aujourd'hui.
        for ($i = 0; $i < 30; $i++) {
            $dateStr = $period->format('Y-m-d');
            $salesTrendLabels[] = $period->format('d/m'); // Formate pour l'affichage.
            $salesDataForDate = $salesTrendRaw->firstWhere('date', $dateStr);
            $salesTrendData[] = $salesDataForDate ? $salesDataForDate->total_sales : 0; // 0 si aucune vente ce jour-là.
            $period->addDay();
        }


        // 6. Articles les plus vendus des 30 derniers jours (basé sur Factures)
        $bestSellingArticlesRaw = DB::table('article_facture')
            ->select('articles.name', DB::raw('SUM(article_facture.quantite) as total_quantity_sold'))
            ->join('articles', 'article_facture.article_id', '=', 'articles.id')
            ->join('factures', 'article_facture.facture_id', '=', 'factures.id')
            ->where('factures.date_facture', '>=', Carbon::now()->subDays(30))
            ->groupBy('articles.id', 'articles.name') // Groupé par ID et nom de l'article.
            ->orderByDesc('total_quantity_sold') // Trie par quantité vendue, décroissant.
            ->limit(5) // Limite aux 5 meilleurs.
            ->get();
        
        $bestSellingArticlesLabels = $bestSellingArticlesRaw->pluck('name')->toArray();
        $bestSellingArticlesData = $bestSellingArticlesRaw->pluck('total_quantity_sold')->toArray();

        // --- Nouvelles Statistiques pour les Commandes E-commerce ---

        // 7. Nombre total de commandes e-commerce payées des 30 derniers jours
        $totalEcommerceOrdersLast30Days = Order::where('created_at', '>=', Carbon::now()->subDays(30))
                                              ->where('payment_status', 'paid') // Prend en compte uniquement les commandes payées.
                                              ->count();

        // 8. Revenu total des commandes e-commerce des 30 derniers jours
        $totalEcommerceRevenueLast30Days = Order::where('created_at', '>=', Carbon::now()->subDays(30))
                                               ->where('payment_status', 'paid')
                                               ->sum('total_amount');

        // 9. Tendance des ventes e-commerce des 30 derniers jours
        $ecommerceSalesTrendRaw = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $ecommerceSalesTrendLabels = [];
        $ecommerceSalesTrendData = [];
        $ecomPeriod = Carbon::now()->subDays(29); // Commence il y a 29 jours.
        for ($i = 0; $i < 30; $i++) {
            $dateStr = $ecomPeriod->format('Y-m-d');
            $ecommerceSalesTrendLabels[] = $ecomPeriod->format('d/m'); // Format pour l'affichage.
            $ecomSalesDataForDate = $ecommerceSalesTrendRaw->firstWhere('date', $dateStr);
            $ecommerceSalesTrendData[] = $ecomSalesDataForDate ? $ecomSalesDataForDate->total_sales : 0;
            $ecomPeriod->addDay();
        }

        // 10. Produits e-commerce les plus vendus des 30 derniers jours
        $bestSellingEcommerceProductsRaw = OrderItem::select('articles.name', DB::raw('SUM(order_items.quantity) as total_quantity_sold'))
            ->join('articles', 'order_items.article_id', '=', 'articles.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', Carbon::now()->subDays(30))
            ->where('orders.payment_status', 'paid') // Uniquement les commandes payées.
            ->groupBy('articles.id', 'articles.name')
            ->orderByDesc('total_quantity_sold')
            ->limit(5) // Limite aux 5 meilleurs.
            ->get();

        $bestSellingEcommerceProductsLabels = $bestSellingEcommerceProductsRaw->pluck('name')->toArray();
        $bestSellingEcommerceProductsData = $bestSellingEcommerceProductsRaw->pluck('total_quantity_sold')->toArray();

        // Transmet toutes les données calculées à la vue.
        return view('statistiques.index', compact(
            'totalArticlesInStock',
            'articlesPerCategoryLabels',
            'articlesPerCategoryData',
            'lowStockArticles',
            'totalSalesRevenueLast30Days', // Facture-based
            'salesTrendLabels',            // Facture-based
            'salesTrendData',              // Facture-based
            'bestSellingArticlesLabels',   // Facture-based
            'bestSellingArticlesData',     // Facture-based

            // E-commerce Order Stats
            'totalEcommerceOrdersLast30Days',
            'totalEcommerceRevenueLast30Days',
            'ecommerceSalesTrendLabels',
            'ecommerceSalesTrendData',
            'bestSellingEcommerceProductsLabels',
            'bestSellingEcommerceProductsData'
        ));
    }
}
