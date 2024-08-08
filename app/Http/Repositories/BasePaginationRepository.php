<?php

namespace App\Http\Repositories;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class BasePaginationRepository extends BaseRepository
{
    public function paginate(array $attributes, callable $sortCallback): Paginator
    {
        return $this->getSearchQuery($this->getModelClass()::query(), $attributes)
                    ->when($attributes['sort'] ?? null, function ($query) use ($attributes, $sortCallback) {
                        $sortCallback($query);
                    })
                    ->paginate($attributes['size'] ?? 10, '*', $attributes['page'] ?? 1);
    }

    protected function getModelClass(): string
    {
        return $this->model;
    }

    protected function getSearchQuery(Builder $builder, array $attributes): Builder
    {
        return $builder;
    }
}
