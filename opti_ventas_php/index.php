<?php

declare(strict_types=1);

/**
 * Opti Ventas — Punto de entrada único (front controller).
 *
 * Este archivo vive en la raíz del proyecto para que el host de
 * Laragon (p. ej. http://opti_ventas_php.test/) acceda directamente
 * a la aplicación sin configuración adicional.
 */

require __DIR__ . '/app/bootstrap.php';

require config_path('routes.php');

clear_request_state();
