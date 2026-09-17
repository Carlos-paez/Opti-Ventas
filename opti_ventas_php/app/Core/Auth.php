<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

final class Auth
{
    private static ?int $loadedId = null;
    private static ?array $loadedUser = null;

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function user(): ?array
    {
        $id = self::id();

        if ($id === null) {
            return null;
        }

        if (self::$loadedId !== $id) {
            self::$loadedId = $id;
            self::$loadedUser = User::find($id);
        }

        return self::$loadedUser;
    }

    public static function isAdmin(): bool
    {
        $user = self::user();

        return $user !== null && $user['role'] === User::ROLE_ADMIN;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        self::$loadedId = null;
        self::$loadedUser = null;
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id']);
        self::$loadedId = null;
        self::$loadedUser = null;
    }
}