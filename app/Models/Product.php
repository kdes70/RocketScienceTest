<?php

namespace App\Models;

use App\Services\Filters\Contracts\Filterable;
use App\Services\Filters\ProductFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model implements Filterable
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'quantity'];

    public function propertyValues(): BelongsToMany
    {
        return $this->belongsToMany(PropertyValue::class, 'product_property_values');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return app(ProductFilter::class)->apply($query, $filters);
    }
}
