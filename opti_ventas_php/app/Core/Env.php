<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Cargador de archivos `.env` en PHP puro (sin librerías externas).
 *
 * Soporta:
 *   - Líneas vacías y comentarios con `#`
 *   - Valores entre comillas simples o dobles
 *   - No sobrescribe variables ya definidas en el entorno real
 */
final class Env
{
    /**
     * Carga un archivo `.env` en `$_ENV` y `getenv()`.
     */
    public static function load(string $path): void
    {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $key = trim($key);
            $value = self::unquote(trim($value));

            if ($key === '' || getenv($key) !== false || array_key_exists($key, $_ENV)) {
                continue;
            }

            $_ENV[$key] = $value;
            putenv($key . '=' . $value);
        }
    }

    /**
     * Elimina las comillas que envuelven un valor, si las hay.
     */
    private static function unquote(string $value): string
    {
        if (strlen($value) < 2) {
            return $value;
        }

        $first = $value[0];
        $last = substr($value, -1);

        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
