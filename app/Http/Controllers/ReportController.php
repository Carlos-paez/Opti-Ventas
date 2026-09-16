<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $summary = $this->buildSummary($request);
        $startDate = $summary['from'] ?? '';
        $endDate = $summary['to'] ?? '';
        $topProducts = $summary['top_products'] ?? collect();

        return view('reports.index', compact('summary', 'startDate', 'endDate', 'topProducts'));
    }

    public function summary(Request $request): JsonResponse
    {
        return response()->json($this->buildSummary($request));
    }

    protected function buildSummary(Request $request): array
    {
        $from = $request->date('from')?->startOfDay();
        $to = $request->date('to')?->endOfDay();

        $sales = Sale::completed()
            ->when($from, fn ($query) => $query->where('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('created_at', '<=', $to));

        $totalSales = (clone $sales)->count();
        $revenue = (clone $sales)->sum('total');

        $topProducts = Product::query()
            ->select('products.*')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity')
            ->selectRaw('SUM(sale_items.line_total) as total_revenue')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->when($from, fn ($query) => $query->where('sales.created_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('sales.created_at', '<=', $to))
            ->groupBy('products.id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return [
            'from' => $from?->toDateString(),
            'to' => $to?->toDateString(),
            'totalSales' => $totalSales,
            'revenue' => round($revenue, 2),
            'totalRevenue' => round($revenue, 2),
            'avgTicket' => $totalSales > 0 ? round($revenue / $totalSales, 2) : 0,
            'totalProfit' => 0,
            'top_products' => $topProducts,
        ];
    }
}
