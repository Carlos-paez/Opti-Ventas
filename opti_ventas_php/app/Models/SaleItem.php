<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class SaleItem
{
    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, line_total, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $data['sale_id'],
                $data['product_id'],
                $data['quantity'],
                $data['unit_price'],
                $data['line_total'],
                now(),
                now(),
            ]
        );
    }
}