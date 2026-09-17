<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class StockMovement
{
    public const TYPE_IN = 'in';
    public const TYPE_OUT = 'out';
    public const TYPE_ADJUST = 'adjust';
}