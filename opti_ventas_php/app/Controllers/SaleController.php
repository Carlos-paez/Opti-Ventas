<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use App\Models\Product;
use App\Models\Sale;

final class SaleController
{
    public function index(): void
    {
        $page = max(1, (int) query('page', 1));
        $filters = [
            'search' => trim((string) query('search', '')),
            'from' => query('from', '') !== '' ? (string) query('from') : null,
            'to' => query('to', '') !== '' ? (string) query('to') : null,
            'status' => query('status', '') !== '' ? (string) query('status') : null,
        ];

        $paged = Sale::all($filters, $page, 15);

        View::render('sales/index', [
            'sales' => $paged['items'],
            'total' => $paged['total'],
            'page' => $page,
            'perPage' => 15,
            'filters' => array_map(fn ($v) => (string) ($v ?? ''), $filters),
        ]);
    }

    public function show(int $id): void
    {
        $sale = Sale::find($id);

        if ($sale === null) {
            flash('error', 'La venta no existe.');
            redirect('/sales');
        }

        $sale['items'] = Sale::items($id);

        View::render('sales/show', ['sale' => $sale]);
    }

    public function void(int $id): void
    {
        $sale = Sale::find($id);

        if ($sale === null) {
            flash('error', 'La venta no existe.');
            back();
        }

        if ($sale['status'] === Sale::STATUS_VOIDED) {
            flash('error', 'Esta venta ya fue anulada.');
            back();
        }

        Database::transaction(function () use ($sale) {
            $items = Sale::items((int) $sale['id']);

            foreach ($items as $item) {
                if ($item['product_id'] !== null) {
                    Product::adjustStock(
                        (int) $item['product_id'],
                        (int) $item['quantity'],
                        'in',
                        "Anulación venta #{$sale['id']}",
                        Auth::id(),
                    );
                }
            }

            Sale::markVoided((int) $sale['id']);
        });

        flash('success', 'Venta anulada y stock restaurado.');
        redirect('/sales');
    }
}