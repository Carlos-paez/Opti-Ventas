<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Setting
{
    private static array $cache = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        $value = Database::value('SELECT value FROM settings WHERE `key` = ?', [$key]);
        self::$cache[$key] = $value === null ? $default : $value;

        return self::$cache[$key];
    }

    public static function set(string $key, string $value): void
    {
        Database::run(
            'INSERT INTO settings (`key`, `value`, created_at, updated_at)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), updated_at = VALUES(updated_at)',
            [$key, $value, now(), now()]
        );

        self::$cache[$key] = $value;
    }

    public static function allKeys(array $keys): array
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = self::get($key) ?? '';
        }

        return $result;
    }
}