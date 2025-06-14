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

class StatistiqueController extends Controller
{
    public function index()
    {
        // --- Existing Stats (mostly inventory and Facture-based sales) ---
        // 1. totalArticlesInStock
        $totalArticlesInStock = Article::sum('quantite');

        // 2. articlesPerCategory
        $categories = Categorie::withCount('articles')->get();
        $articlesPerCategoryLabels = $categories->pluck('name')->toArray();
        $articlesPerCategoryData = $categories->pluck('articles_count')->toArray();

        // 3. lowStockArticles
        $lowStockThreshold = 10;
        $lowStockArticles = Article::where('quantite', '<', $lowStockThreshold)
                                   ->select('name', 'quantite')
                                   ->orderBy('quantite', 'asc')
                                   ->get();

        // 4. totalSalesRevenueLast30Days
        $totalSalesRevenueLast30Days = Facture::where('date_facture', '>=', Carbon::now()->subDays(30))
                                              ->sum('montant_ttc');

        // 5. salesTrendLast30Days
        $salesTrendRaw = Facture::select(
                DB::raw('DATE(date_facture) as date'),
                DB::raw('SUM(montant_ttc) as total_sales')
            )
            ->where('date_facture', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $salesTrendLabels = [];
        $salesTrendData = [];
        // Initialize with all dates in the last 30 days to ensure continuity in the chart
        $period = Carbon::now()->subDays(29); // Start from 29 days ago to include today
        for ($i = 0; $i < 30; $i++) {
            $dateStr = $period->format('Y-m-d');
            $salesTrendLabels[] = $period->format('d/m'); // Format for display
            $salesDataForDate = $salesTrendRaw->firstWhere('date', $dateStr);
            $salesTrendData[] = $salesDataForDate ? $salesDataForDate->total_sales : 0;
            $period->addDay();
        }


        // 6. bestSellingArticlesLast30Days (Facture-based)
        $bestSellingArticlesRaw = DB::table('article_facture')
            ->select('articles.name', DB::raw('SUM(article_facture.quantite) as total_quantity_sold'))
            ->join('articles', 'article_facture.article_id', '=', 'articles.id')
            ->join('factures', 'article_facture.facture_id', '=', 'factures.id')
            ->where('factures.date_facture', '>=', Carbon::now()->subDays(30))
            ->groupBy('articles.id', 'articles.name')
            ->orderByDesc('total_quantity_sold')
            ->limit(5)
            ->get();
        
        $bestSellingArticlesLabels = $bestSellingArticlesRaw->pluck('name')->toArray();
        $bestSellingArticlesData = $bestSellingArticlesRaw->pluck('total_quantity_sold')->toArray();

        // --- New E-commerce Order Stats ---

        // 7. totalEcommerceOrdersLast30Days
        $totalEcommerceOrdersLast30Days = Order::where('created_at', '>=', Carbon::now()->subDays(30))
                                              ->where('payment_status', 'paid') // Consider only paid orders
                                              ->count();

        // 8. totalEcommerceRevenueLast30Days
        $totalEcommerceRevenueLast30Days = Order::where('created_at', '>=', Carbon::now()->subDays(30))
                                               ->where('payment_status', 'paid')
                                               ->sum('total_amount');

        // 9. ecommerceSalesTrendLast30Days
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
        $ecomPeriod = Carbon::now()->subDays(29);
        for ($i = 0; $i < 30; $i++) {
            $dateStr = $ecomPeriod->format('Y-m-d');
            $ecommerceSalesTrendLabels[] = $ecomPeriod->format('d/m');
            $ecomSalesDataForDate = $ecommerceSalesTrendRaw->firstWhere('date', $dateStr);
            $ecommerceSalesTrendData[] = $ecomSalesDataForDate ? $ecomSalesDataForDate->total_sales : 0;
            $ecomPeriod->addDay();
        }

        // 10. bestSellingEcommerceProductsLast30Days
        $bestSellingEcommerceProductsRaw = OrderItem::select('articles.name', DB::raw('SUM(order_items.quantity) as total_quantity_sold'))
            ->join('articles', 'order_items.article_id', '=', 'articles.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', Carbon::now()->subDays(30))
            ->where('orders.payment_status', 'paid')
            ->groupBy('articles.id', 'articles.name')
            ->orderByDesc('total_quantity_sold')
            ->limit(5)
            ->get();

        $bestSellingEcommerceProductsLabels = $bestSellingEcommerceProductsRaw->pluck('name')->toArray();
        $bestSellingEcommerceProductsData = $bestSellingEcommerceProductsRaw->pluck('total_quantity_sold')->toArray();


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
