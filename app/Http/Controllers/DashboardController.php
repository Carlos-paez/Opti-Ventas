<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();

        $todaySalesQuery = Sale::completed()->whereDate('created_at', $today);

        $todaySales = (clone $todaySalesQuery)->count();
        $todayRevenue = (clone $todaySalesQuery)->sum('total');
        $totalProducts = Product::count();
        $lowStockCount = Product::where('stock_min', '>', 0)
            ->whereColumn('stock', '<=', 'stock_min')
            ->count();

        $recentSales = Sale::with(['customer', 'user'])
            ->completed()
            ->latest()
            ->limit(5)
            ->get();

        $lowStockProducts = Product::with('category')
            ->where('stock_min', '>', 0)
            ->whereColumn('stock', '<=', 'stock_min')
            ->orderByRaw('stock - stock_min')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'todaySales', 'todayRevenue', 'totalProducts', 'lowStockCount',
            'recentSales', 'lowStockProducts',
        ));
    }
}
