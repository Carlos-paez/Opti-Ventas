<?php

declare(strict_types=1);

/**
 * Acceso a variables de entorno y configuración.
 */

/**
 * Lee una variable de entorno (cargada desde `.env` o del sistema),
 * con conversión automática de "true"/"false"/"null"/cadena vacía.
 */
function env(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? getenv($key);

    if ($value === false || $value === null) {
        return $default;
    }

    if (is_string($value)) {
        return match (strtolower($value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'null', '(null)', '' => null,
            default => $value,
        };
    }

    return $value;
}

/**
 * Accede a un valor de configuración usando notación de punto.
 * Ejemplos: config('app.name'), config('database.host').
 */
function config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS['__config'] ?? [];

    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }

    return $value;
}
