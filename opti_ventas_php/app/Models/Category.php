<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Category
{
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM categories WHERE id = ?', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM categories WHERE slug = ?', [$slug]);
    }

    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO categories (name, slug, description, color, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $data['name'],
                $data['slug'],
                nullable_string($data['description'] ?? null),
                $data['color'] ?? '#6366f1',
                now(),
                now(),
            ]
        );
    }

    public static function update(int $id, array $data): void
    {
        Database::run(
            'UPDATE categories SET name = ?, slug = ?, description = ?, color = ?, updated_at = ? WHERE id = ?',
            [
                $data['name'],
                $data['slug'],
                nullable_string($data['description'] ?? null),
                $data['color'] ?? '#6366f1',
                now(),
                $id,
            ]
        );
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM categories WHERE id = ?', [$id]);
    }

    public static function allOrdered(): array
    {
        return Database::fetchAll('SELECT * FROM categories ORDER BY name ASC');
    }

    public static function all(array $filters, int $page, int $perPage): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['search'])) {
            $conditions[] = '(name LIKE ? OR description LIKE ?)';
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $total = (int) Database::value(
            "SELECT COUNT(*) FROM categories {$where}",
            $params
        );

        $all = array_merge($params, [$perPage, ($page - 1) * $perPage]);
        $rows = Database::fetchAll(
            "SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS products_count
             FROM categories c {$where} ORDER BY c.id DESC LIMIT ? OFFSET ?",
            $all
        );

        return ['items' => $rows, 'total' => $total];
    }

    public static function hasProducts(int $id): bool
    {
        return (int) Database::value('SELECT COUNT(*) FROM products WHERE category_id = ?', [$id]) > 0;
    }
}