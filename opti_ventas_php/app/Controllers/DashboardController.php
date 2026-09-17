<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Product;
use App\Models\Sale;

final class DashboardController
{
    public function index(): void
    {
        $stats = Sale::todayStats();
        $lowStockProducts = Product::lowStock(10);
        $recentSales = Sale::recent(5);

        View::render('dashboard', [
            'todaySales' => $stats['count'],
            'todayRevenue' => $stats['revenue'],
            'totalProducts' => Product::count(),
            'lowStockCount' => Product::countLowStock(),
            'recentSales' => $recentSales,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}