<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Sale;

final class ReportController
{
    public function index(): void
    {
        $from = query('from', '') !== '' ? (string) query('from') : null;
        $to = query('to', '') !== '' ? (string) query('to') : null;

        $summary = Sale::summary($from, $to);

        View::render('reports/index', [
            'summary' => $summary,
            'startDate' => $summary['from'] ?? '',
            'endDate' => $summary['to'] ?? '',
            'topProducts' => $summary['top_products'] ?? [],
        ]);
    }
}