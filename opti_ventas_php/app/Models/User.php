<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class User
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SELLER = 'seller';

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::fetch('SELECT * FROM users WHERE email = ?', [$email]);
    }

    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO users (name, email, password, role, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $data['name'],
                $data['email'],
                $data['password'],
                $data['role'],
                (int) ($data['is_active'] ?? 1),
                now(),
                now(),
            ]
        );
    }

    public static function update(int $id, array $data): void
    {
        $fields = [];
        $params = [];

        foreach ($data as $column => $value) {
            $fields[] = "{$column} = ?";
            $params[] = $value;
        }

        $fields[] = 'updated_at = ?';
        $params[] = now();
        $params[] = $id;

        Database::run('UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?', $params);
    }

    public static function all(array $filters, int $page, int $perPage): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['search'])) {
            $conditions[] = '(name LIKE ? OR email LIKE ?)';
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        if (!empty($filters['role'])) {
            $conditions[] = 'role = ?';
            $params[] = $filters['role'];
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $total = (int) Database::value(
            "SELECT COUNT(*) FROM users {$where}",
            $params
        );

        $all = array_merge($params, [$perPage, ($page - 1) * $perPage]);
        $rows = Database::fetchAll(
            "SELECT * FROM users {$where} ORDER BY id DESC LIMIT ? OFFSET ?",
            $all
        );

        return ['items' => $rows, 'total' => $total];
    }

    public static function toggleActive(int $id): void
    {
        Database::run('UPDATE users SET is_active = NOT is_active, updated_at = ? WHERE id = ?', [now(), $id]);
    }
}