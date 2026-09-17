<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Category;

final class Validator
{
    private const LABELS = [
        'name' => 'Nombre',
        'email' => 'Correo electrónico',
        'password' => 'Contraseña',
        'password_confirmation' => 'Confirmación de contraseña',
        'role' => 'Rol',
        'sku' => 'SKU',
        'barcode' => 'Código de barras',
        'category_id' => 'Categoría',
        'description' => 'Descripción',
        'price' => 'Precio de venta',
        'cost' => 'Costo',
        'stock' => 'Stock',
        'stock_min' => 'Stock mínimo',
        'is_active' => 'Estado',
        'photo' => 'Foto',
        'customer_id' => 'Cliente',
        'products' => 'Productos',
        'items' => 'Productos',
        'quantity' => 'Cantidad',
        'payment_method' => 'Método de pago',
        'discount' => 'Descuento',
        'tax_rate' => 'Tasa de impuesto',
        'notes' => 'Notas',
        'business_name' => 'Nombre del negocio',
        'currency' => 'Moneda',
        'receipt_footer' => 'Pie de recibo',
        'color' => 'Color',
        'phone' => 'Teléfono',
        'address' => 'Dirección',
        'from' => 'Fecha inicio',
        'to' => 'Fecha fin',
        'status' => 'Estado',
    ];

    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $labels = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            $nullable = in_array('nullable', $labels, true);
            $isString = in_array('string', $labels, true);

            if ($nullable && ($value === null || (is_string($value) && trim($value) === ''))) {
                continue;
            }

            if (is_string($value)) {
                $value = trim($value);
            }

            foreach ($labels as $label) {
                if ($label === 'nullable') {
                    continue;
                }

                [$name, $param] = array_pad(explode(':', $label, 2), 2, null);

                $message = self::check($name, $param, $value, $data, $field, $isString);

                if ($message !== null) {
                    $errors[$field] = $message;
                    continue 2;
                }
            }
        }

        return $errors;
    }

    private static function check(string $rule, ?string $param, mixed $value, array $data, string $field, bool $isString = false): ?string
    {
        $label = self::LABELS[$field] ?? self::humanize($field);

        return match ($rule) {
            'required' => ($value === null || (is_string($value) && trim($value) === '')) ? "El campo {$label} es obligatorio." : null,

            'string' => is_string($value) ? null : "El campo {$label} debe ser una cadena de texto.",

            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) ? null : "El campo {$label} debe ser un correo válido.",

            'numeric' => is_numeric($value) ? null : "El campo {$label} debe ser numérico.",

            'integer' => filter_var($value, FILTER_VALIDATE_INT) !== false ? null : "El campo {$label} debe ser un número entero.",

            'boolean' => is_bool($value) || in_array($value, [0, 1, '0', '1', 'true', 'false', 'on', 'off', ''], true) ? null : "El campo {$label} es inválido.",

            'date' => $value === '' || strtotime((string) $value) !== false ? null : "El campo {$label} debe ser una fecha válida.",

            'min' => self::checkMin($param, $value, $label, $isString),

            'max' => self::checkMax($param, $value, $label, $isString),

            'in' => is_string($param) && in_array((string) $value, explode(',', $param), true)
                ? null
                : "El campo {$label} contiene un valor no permitido.",

            'confirmed' => $data[$field . '_confirmation'] !== $value ? 'La confirmación de la contraseña no coincide.' : null,

            'unique' => self::checkUnique($param, $value, $label),

            'exists' => self::checkExists($param, $value, $label),

            default => null,
        };
    }

    private static function humanize(string $field): string
    {
        return ucfirst(str_replace(['_', '-'], ' ', $field));
    }

    private static function checkMin(?string $param, mixed $value, string $label, bool $isString = false): ?string
    {
        if (!is_numeric($param)) {
            return null;
        }

        $min = (float) $param;

        if ($isString) {
            return is_string($value) && mb_strlen($value) < $min
                ? "El campo {$label} debe tener al menos {$min} caracteres."
                : null;
        }

        if (is_numeric($value) && (float) $value < $min) {
            return "El campo {$label} debe ser al menos {$min}.";
        }

        if (is_string($value) && mb_strlen($value) < $min) {
            return "El campo {$label} debe tener al menos {$min} caracteres.";
        }

        return null;
    }

    private static function checkMax(?string $param, mixed $value, string $label, bool $isString = false): ?string
    {
        if (!is_numeric($param)) {
            return null;
        }

        $max = (float) $param;

        if ($isString) {
            return is_string($value) && mb_strlen($value) > $max
                ? "El campo {$label} no debe exceder los {$max} caracteres."
                : null;
        }

        if (is_numeric($value) && (float) $value > $max) {
            return "El campo {$label} no debe ser mayor a {$max}.";
        }

        if (is_string($value) && mb_strlen($value) > $max) {
            return "El campo {$label} no debe exceder los {$max} caracteres.";
        }

        return null;
    }

    private static function checkUnique(?string $param, mixed $value, string $label): ?string
    {
        if (!is_string($param) || $value === null || $value === '') {
            return null;
        }

        $parts = explode(',', $param);
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $parts[0] ?? '');
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $parts[1] ?? 'id');
        $ignore = $parts[2] ?? null;

        if ($table === '' || $column === '') {
            return null;
        }

        $params = [$value];
        $ignoreClause = '';

        if ($ignore !== null && $ignore !== '') {
            $params[] = $ignore;
            $ignoreClause = " AND id != ?";
        }

        $exists = Database::value("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?{$ignoreClause}", $params);

        return $exists > 0 ? "El valor del campo {$label} ya está en uso." : null;
    }

    private static function checkExists(?string $param, mixed $value, string $label): ?string
    {
        if (!is_string($param) || $value === null || $value === '') {
            return null;
        }

        $parts = explode(',', $param);
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $parts[0] ?? '');
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $parts[1] ?? 'id');

        if ($table === '' || $column === '') {
            return null;
        }

        $exists = Database::value("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?", [$value]);

        return $exists > 0 ? null : "El campo {$label} seleccionado no existe.";
    }
}