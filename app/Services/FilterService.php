<?php

namespace App\Services;

use App\Services\Filters\Contracts\Filterable;
use Illuminate\Database\Eloquent\Builder;

class FilterService
{
    public function filter(Filterable $model, array $filters): Builder
    {
        $query = $model::query();

        $filteredQuery = $query->filter($filters);

        // Оптимизация запроса для больших объемов данных
        return $filteredQuery->select($model->getTable().'.*')
            ->leftJoin('product_property_values', $model->getTable().'.id', '=', 'product_property_values.product_id')
            ->leftJoin('property_values', 'product_property_values.property_value_id', '=', 'property_values.id')
            ->leftJoin('properties', 'property_values.property_id', '=', 'properties.id')
            ->groupBy($model->getTable().'.id');
    }
}
