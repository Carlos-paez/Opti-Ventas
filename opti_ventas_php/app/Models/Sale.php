<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Sale
{
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_VOIDED = 'voided';

    public const METHODS = ['cash', 'card', 'transfer', 'other'];

    public const BASE_SELECT = 's.*, c.name AS customer_name, u.name AS user_name';

    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO sales (user_id, customer_id, subtotal, discount, tax, total, payment_method, status, notes, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['user_id'],
                $data['customer_id'] ?? null,
                $data['subtotal'],
                $data['discount'],
                $data['tax'],
                $data['total'],
                $data['payment_method'],
                $data['status'],
                $data['notes'] ?? null,
                now(),
                now(),
            ]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch(
            'SELECT ' . self::BASE_SELECT . ' FROM sales s
             LEFT JOIN customers c ON c.id = s.customer_id
             LEFT JOIN users u ON u.id = s.user_id
             WHERE s.id = ?',
            [$id]
        );
    }

    public static function markVoided(int $id): void
    {
        Database::run('UPDATE sales SET status = ?, updated_at = ? WHERE id = ?', [self::STATUS_VOIDED, now(), $id]);
    }

    public static function all(array $filters, int $page, int $perPage): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['search'])) {
            $conditions[] = '(s.id = ? OR c.name LIKE ? OR u.name LIKE ?)';
            $params[] = $filters['search'];
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        if (!empty($filters['from'])) {
            $conditions[] = 'DATE(s.created_at) >= ?';
            $params[] = $filters['from'];
        }

        if (!empty($filters['to'])) {
            $conditions[] = 'DATE(s.created_at) <= ?';
            $params[] = $filters['to'];
        }

        if (!empty($filters['status'])) {
            $conditions[] = 's.status = ?';
            $params[] = $filters['status'];
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $total = (int) Database::value(
            "SELECT COUNT(*) FROM sales s {$where}",
            $params
        );

        $all = array_merge($params, [$perPage, ($page - 1) * $perPage]);
        $items = Database::fetchAll(
            "SELECT " . self::BASE_SELECT . " FROM sales s
             LEFT JOIN customers c ON c.id = s.customer_id
             LEFT JOIN users u ON u.id = s.user_id
             {$where} ORDER BY s.id DESC LIMIT ? OFFSET ?",
            $all
        );

        return ['items' => $items, 'total' => $total];
    }

    public static function todayStats(): array
    {
        $row = Database::fetch(
            "SELECT COUNT(*) AS count, COALESCE(SUM(total), 0) AS revenue
             FROM sales WHERE status = ? AND DATE(created_at) = CURDATE()",
            [self::STATUS_COMPLETED]
        );

        return [
            'count' => (int) ($row['count'] ?? 0),
            'revenue' => (float) ($row['revenue'] ?? 0),
        ];
    }

    public static function recent(int $limit = 5): array
    {
        return Database::fetchAll(
            'SELECT ' . self::BASE_SELECT . ' FROM sales s
             LEFT JOIN customers c ON c.id = s.customer_id
             LEFT JOIN users u ON u.id = s.user_id
             WHERE s.status = ? ORDER BY s.id DESC LIMIT ?',
            [self::STATUS_COMPLETED, $limit]
        );
    }

    public static function items(int $saleId): array
    {
        return Database::fetchAll(
            'SELECT si.product_id, si.quantity, si.unit_price, si.line_total, p.name AS product_name
             FROM sale_items si
             LEFT JOIN products p ON p.id = si.product_id
             WHERE si.sale_id = ? ORDER BY si.id ASC',
            [$saleId]
        );
    }

    public static function summary(?string $from, ?string $to): array
    {
        $conditions = ['status = ?'];
        $params = [self::STATUS_COMPLETED];

        if ($from !== null) {
            $conditions[] = 'created_at >= ?';
            $params[] = $from . ' 00:00:00';
        }

        if ($to !== null) {
            $conditions[] = 'created_at <= ?';
            $params[] = $to . ' 23:59:59';
        }

        $where = 'WHERE ' . implode(' AND ', $conditions);

        $row = Database::fetch(
            "SELECT COUNT(*) AS count, COALESCE(SUM(total), 0) AS revenue FROM sales {$where}",
            $params
        );

        $count = (int) ($row['count'] ?? 0);
        $revenue = (float) ($row['revenue'] ?? 0);

        $topParams = [self::STATUS_COMPLETED];

        if ($from !== null) {
            $topParams[] = $from . ' 00:00:00';
        }

        if ($to !== null) {
            $topParams[] = $to . ' 23:59:59';
        }

        $topParams[] = 10;

        $top = Database::fetchAll(
            'SELECT p.id, p.name, SUM(si.quantity) AS total_quantity, SUM(si.line_total) AS total_revenue
             FROM products p
             JOIN sale_items si ON si.product_id = p.id
             JOIN sales s ON s.id = si.sale_id
             WHERE s.status = ?'
            . ($from !== null ? ' AND s.created_at >= ?' : '')
            . ($to !== null ? ' AND s.created_at <= ?' : '')
            . ' GROUP BY p.id, p.name ORDER BY total_quantity DESC LIMIT ?',
            $topParams
        );

        return [
            'from' => $from,
            'to' => $to,
            'totalSales' => $count,
            'revenue' => round($revenue, 2),
            'totalRevenue' => round($revenue, 2),
            'avgTicket' => $count > 0 ? round($revenue / $count, 2) : 0,
            'totalProfit' => 0,
            'top_products' => $top,
        ];
    }
}