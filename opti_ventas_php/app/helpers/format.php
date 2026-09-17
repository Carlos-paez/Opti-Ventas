<?php

declare(strict_types=1);

/**
 * Utilidades de formato y valores de la configuración del negocio.
 */

function nullable_string(?string $value): ?string
{
    if ($value === null) {
        return null;
    }
    $trimmed = trim($value);

    return $trimmed === '' ? null : $trimmed;
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

function slugify(string $text): string
{
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = strtr($text, [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'ü' => 'u', 'ñ' => 'n', 'ç' => 'c',
    ]);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    return trim((string) $text, '-');
}

function money_symbol(): string
{
    static $symbol = null;

    if ($symbol !== null) {
        return $symbol;
    }

    $currency = strtoupper(trim((string) (setting('currency', 'MXN') ?? 'MXN')));

    $symbol = match ($currency) {
        'USD', 'MXN', 'CAD', 'AUD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'JPY', 'CNY' => '¥',
        default => $currency,
    };

    return $symbol;
}

function money(float $amount): string
{
    return money_symbol() . number_format($amount, 2);
}

function setting(string $key, mixed $default = null): mixed
{
    return \App\Models\Setting::get($key, $default);
}
