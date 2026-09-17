<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Opti Ventas'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),

    // Déjalo vacío para detectar automáticamente el host
    // (recomendado con Laragon: http://opti_ventas_php.test/).
    'url' => env('APP_URL', ''),
];
