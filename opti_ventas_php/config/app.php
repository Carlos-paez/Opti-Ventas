<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Opti Ventas'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),

    // Si está vacío, se detecta automáticamente desde el dominio actual.
    // En InfinityFree suele ser algo como https://tu-dominio.com o
    // https://tu-dominio.com/subdirectorio si la app va en una carpeta.
    'url' => env('APP_URL', ''),
];
