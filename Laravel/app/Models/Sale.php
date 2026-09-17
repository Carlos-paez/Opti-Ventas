<?php

namespace App\Models;

use Database\Factories\SaleFactory;
use Illuminate\Database\Eloquent\Attributes\Casts;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'customer_id', 'subtotal', 'discount', 'tax',
    'total', 'payment_method', 'status', 'notes',
])]
#[Casts([
    'subtotal' => 'decimal:2',
    'discount' => 'decimal:2',
    'tax' => 'decimal:2',
    'total' => 'decimal:2',
])]
class Sale extends Model
{
    /** @use HasFactory<SaleFactory> */
    use HasFactory;

    public function scopeCompleted($query): mixed
    {
        return $query->where('status', 'completed');
    }

    public function scopeSearch($query, ?string $term): mixed
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('id', $term)
                ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$term}%"))
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%"));
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class)->with('product');
    }

    public function getFormattedTotalAttribute(): string
    {
        return '$'.number_format((float) $this->total, 2);
    }

    public function getIsVoidedAttribute(): bool
    {
        return $this->status === 'voided';
    }
}
