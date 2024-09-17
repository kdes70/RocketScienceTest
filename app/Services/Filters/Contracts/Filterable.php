<?php

namespace App\Services\Filters\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Filterable
{
    public function scopeFilter(Builder $query, array $filters): Builder;
}
