<?php

namespace App\Services\Filters;

use Illuminate\Database\Eloquent\Builder;

abstract class AbstractFilter
{
    protected array $filters = [];

    public function apply(Builder $query, array $filters): Builder
    {
        $this->filters = $filters;

        foreach ($this->getFilters() as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->$filter($query, $value);
            }
        }

        return $query;
    }

    protected function getFilters(): array
    {
        return array_filter($this->filters);
    }
}
