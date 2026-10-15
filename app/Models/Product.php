<?php

namespace App\Models;

use App\Enums\BeanType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'type', 'origin', 'roast_level', 'flavour', 'description', 'image', 'is_active'])]
class Product extends Model
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
            'type' => BeanType::class,
            'flavour' => 'array',
            'is_active' => 'boolean',
        ];
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('weight_grams');
    }

    public function brewMethods(): BelongsToMany
    {
        return $this->belongsToMany(BrewMethod::class);
    }

    public static function getAllProducts(): Collection
    {
        return static::query()
            ->active()
            ->with(['variants', 'brewMethods'])
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }

    public static function getFeaturedProducts(int $limit = 3): Collection
    {
        return static::query()
            ->active()
            ->with(['variants', 'brewMethods'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
