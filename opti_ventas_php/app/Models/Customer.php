<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Customer
{
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM customers WHERE id = ?', [$id]);
    }

    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO customers (name, phone, email, address, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $data['name'],
                nullable_string($data['phone'] ?? null),
                nullable_string($data['email'] ?? null),
                nullable_string($data['address'] ?? null),
                now(),
                now(),
            ]
        );
    }

    public static function update(int $id, array $data): void
    {
        Database::run(
            'UPDATE customers SET name = ?, phone = ?, email = ?, address = ?, updated_at = ? WHERE id = ?',
            [
                $data['name'],
                nullable_string($data['phone'] ?? null),
                nullable_string($data['email'] ?? null),
                nullable_string($data['address'] ?? null),
                now(),
                $id,
            ]
        );
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM customers WHERE id = ?', [$id]);
    }

    public static function allOrdered(): array
    {
        return Database::fetchAll('SELECT * FROM customers ORDER BY name ASC');
    }

    public static function all(?string $search, int $page, int $perPage): array
    {
        $where = '';
        $params = [];

        if (!empty($search)) {
            $where = 'WHERE (name LIKE ? OR phone LIKE ? OR email LIKE ?)';
            $params = ["%{$search}%", "%{$search}%", "%{$search}%"];
        }

        $total = (int) Database::value(
            "SELECT COUNT(*) FROM customers {$where}",
            $params
        );

        $all = array_merge($params, [$perPage, ($page - 1) * $perPage]);
        $items = Database::fetchAll(
            "SELECT customers.*, (SELECT COUNT(*) FROM sales s WHERE s.customer_id = customers.id) AS sales_count
             FROM customers {$where} ORDER BY id DESC LIMIT ? OFFSET ?",
            $all
        );

        return ['items' => $items, 'total' => $total];
    }

    public static function hasSales(int $id): bool
    {
        return (int) Database::value('SELECT COUNT(*) FROM sales WHERE customer_id = ?', [$id]) > 0;
    }
}