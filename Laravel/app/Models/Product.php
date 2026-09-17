<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Casts;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'name', 'slug', 'sku', 'barcode', 'category_id', 'description',
    'price', 'cost', 'stock', 'stock_min', 'photo_path', 'is_active',
])]
#[Casts([
    'price' => 'decimal:2',
    'cost' => 'decimal:2',
    'stock' => 'integer',
    'stock_min' => 'integer',
    'is_active' => 'boolean',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->slug = $product->slug ?: Str::slug($product->name);
        });
    }

    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, ?string $term): mixed
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('barcode', 'like', "%{$term}%");
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? Storage::url($this->photo_path) : null;
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$'.number_format((float) $this->price, 2);
    }

    public function getLowStockAttribute(): bool
    {
        return $this->stock_min > 0 && $this->stock <= $this->stock_min;
    }

    public function decreaseStock(int $quantity, string $reason, ?int $userId = null): void
    {
        $this->stock -= $quantity;
        $this->save();

        $this->stockMovements()->create([
            'type' => 'out',
            'quantity' => $quantity,
            'reason' => $reason,
            'user_id' => $userId,
        ]);
    }

    public function increaseStock(int $quantity, string $reason, ?int $userId = null): void
    {
        $this->stock += $quantity;
        $this->save();

        $this->stockMovements()->create([
            'type' => 'in',
            'quantity' => $quantity,
            'reason' => $reason,
            'user_id' => $userId,
        ]);
    }
}
