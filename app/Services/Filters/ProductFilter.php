<?php

namespace App\Services\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends AbstractFilter
{
    protected function properties(Builder $query, array $properties): void
    {
        foreach ($properties as $propertyName => $values) {
            $query->whereHas('propertyValues.property', function (Builder $query) use ($propertyName, $values) {
                $query->where('name', $propertyName)
                    ->whereIn('value', $values);
            });
        }
    }

    protected function price(Builder $query, array $price): void
    {
        if (isset($price['min'])) {
            $query->where('price', '>=', $price['min']);
        }
        if (isset($price['max'])) {
            $query->where('price', '<=', $price['max']);
        }
    }

    protected function quantity(Builder $query, array $quantity): void
    {
        if (isset($quantity['min'])) {
            $query->where('quantity', '>=', $quantity['min']);
        }
        if (isset($quantity['max'])) {
            $query->where('quantity', '<=', $quantity['max']);
        }
    }

    // Добавьте другие методы фильтрации по необходимости
}
