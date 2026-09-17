<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Product
{
    public const BASE_SELECT = 'p.*, c.name AS category_name';

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ?', [$id]);
    }

    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO products (name, slug, sku, barcode, category_id, description, price, cost, stock, stock_min, photo_path, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['name'],
                $data['slug'],
                nullable_string($data['sku'] ?? null),
                nullable_string($data['barcode'] ?? null),
                !empty($data['category_id']) ? (int) $data['category_id'] : null,
                nullable_string($data['description'] ?? null),
                $data['price'],
                $data['cost'] ?? 0,
                $data['stock'] ?? 0,
                $data['stock_min'] ?? 5,
                nullable_string($data['photo_path'] ?? null),
                (int) ($data['is_active'] ?? 1),
                now(),
                now(),
            ]
        );
    }

    public static function update(int $id, array $data): void
    {
        $data['updated_at'] = now();
        $fields = [];
        $params = [];

        $nullableColumns = ['sku', 'barcode', 'description', 'photo_path'];

        foreach (['name', 'slug', 'sku', 'barcode', 'category_id', 'description', 'price', 'cost', 'stock', 'stock_min', 'photo_path', 'is_active', 'updated_at'] as $column) {
            if (!array_key_exists($column, $data)) {
                continue;
            }
            $val = $data[$column];
            if (in_array($column, $nullableColumns, true)) {
                $val = nullable_string($val);
            } elseif ($column === 'category_id') {
                $val = !empty($val) ? (int) $val : null;
            }
            $fields[] = "{$column} = ?";
            $params[] = $val;
        }

        if (!$fields) {
            return;
        }

        $params[] = $id;
        Database::run('UPDATE products SET ' . implode(', ', $fields) . ' WHERE id = ?', $params);
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM products WHERE id = ?', [$id]);
    }

    public static function all(array $filters, int $page, int $perPage): array
    {
        [$where, $params] = self::buildWhere($filters, true);

        $total = (int) Database::value(
            "SELECT COUNT(*) FROM products p {$where}",
            $params
        );

        $all = array_merge($params, [$perPage, ($page - 1) * $perPage]);
        $items = Database::fetchAll(
            "SELECT " . self::BASE_SELECT . " FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             {$where} ORDER BY p.id DESC LIMIT ? OFFSET ?",
            $all
        );

        return ['items' => $items, 'total' => $total];
    }

    public static function activeForPos(?int $categoryId, int $page, int $perPage): array
    {
        $where = 'WHERE p.is_active = 1';
        $params = [];

        if ($categoryId !== null) {
            $where .= ' AND p.category_id = ?';
            $params[] = $categoryId;
        }

        $total = (int) Database::value(
            "SELECT COUNT(*) FROM products p {$where}",
            $params
        );

        $all = array_merge($params, [$perPage, ($page - 1) * $perPage]);
        $items = Database::fetchAll(
            "SELECT " . self::BASE_SELECT . " FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             {$where} ORDER BY p.id DESC LIMIT ? OFFSET ?",
            $all
        );

        return ['items' => $items, 'total' => $total];
    }

    public static function count(): int
    {
        return (int) Database::value('SELECT COUNT(*) FROM products');
    }

    public static function countLowStock(): int
    {
        return (int) Database::value(
            'SELECT COUNT(*) FROM products WHERE stock_min > 0 AND stock <= stock_min'
        );
    }

    public static function lowStock(int $limit = 10): array
    {
        return Database::fetchAll(
            'SELECT p.*, c.name AS category_name FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.stock_min > 0 AND p.stock <= p.stock_min
             ORDER BY (p.stock - p.stock_min) ASC LIMIT ?',
            [$limit]
        );
    }

    public static function hasSaleItems(int $id): bool
    {
        return (int) Database::value('SELECT COUNT(*) FROM sale_items WHERE product_id = ?', [$id]) > 0;
    }

    public static function adjustStock(int $id, int $quantity, string $type, string $reason, ?int $userId): void
    {
        $sign = $type === 'out' ? -1 : 1;

        Database::transaction(function () use ($id, $quantity, $sign, $type, $reason, $userId) {
            Database::run(
                'UPDATE products SET stock = stock + (? * ?), updated_at = ? WHERE id = ?',
                [$sign, $quantity, now(), $id]
            );

            Database::insert(
                'INSERT INTO stock_movements (product_id, type, quantity, reason, user_id, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$id, $type, $quantity, $reason, $userId, now(), now()]
            );
        });
    }

    private static function buildWhere(array $filters, bool $withStatus): array
    {
        $conditions = [];
        $params = [];

        if ($withStatus && isset($filters['status']) && in_array($filters['status'], ['active', 'inactive'], true)) {
            $conditions[] = 'p.is_active = ?';
            $params[] = $filters['status'] === 'active' ? 1 : 0;
        }

        if (!empty($filters['category_id'])) {
            $conditions[] = 'p.category_id = ?';
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = '(p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)';
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return [$where, $params];
    }
}