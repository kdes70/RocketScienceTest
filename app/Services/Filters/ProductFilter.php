<?php

namespace App\Services\Filters;


use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends AbstractFilter
{
    protected function properties(Builder $query, array $properties): void
    {
        foreach ($properties as $propertyName => $values) {
            $query->whereHas('propertyValues', function (Builder $query) use ($propertyName, $values) {
                $query->whereHas('property', function (Builder $query) use ($propertyName) {
                    $query->where('name', $propertyName);
                })->whereIn('value', $values);
            });
        }
    }

    // Добавьте другие методы фильтрации по необходимости
}