<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'weight_grams', 'price', 'stock'])]
class ProductVariant extends Model
{
    use HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weight_grams' => 'integer',
            'price' => 'integer',
            'stock' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function formattedPrice(): string
    {
        return number_format($this->price / 100, 2);
    }

    public function weightLabel(): string
    {
        return $this->weight_grams >= 1000 && $this->weight_grams % 1000 === 0
            ? ($this->weight_grams / 1000).'kg'
            : $this->weight_grams.'g';
    }
}
