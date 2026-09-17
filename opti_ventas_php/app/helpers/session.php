<?php

declare(strict_types=1);

/**
 * Mensajes flash, errores de validación y old input (sesión).
 */

function flash(string $key, mixed $value): void
{
    $_SESSION['flash'][$key] = $value;
}

function consume_flash(?string $key = null): mixed
{
    if ($key === null) {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        return $flash;
    }

    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);

    return $value;
}

function flash_errors(array $errors): void
{
    $_SESSION['errors'] = $errors;
}

function errors(): array
{
    return $_SESSION['errors'] ?? [];
}

function has_error(string $key): bool
{
    return isset($_SESSION['errors'][$key]);
}

function field_error(string $key): string
{
    return (string) ($_SESSION['errors'][$key] ?? '');
}

function old_input(array $data): void
{
    $_SESSION['old'] = $data;
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['old'][$key] ?? $default;
}

function clear_request_state(): void
{
    unset($_SESSION['errors'], $_SESSION['old']);
}
