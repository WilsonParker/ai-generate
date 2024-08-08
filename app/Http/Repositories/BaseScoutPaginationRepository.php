<?php

namespace App\Http\Repositories;

use Illuminate\Contracts\Pagination\Paginator;

class BaseScoutPaginationRepository extends BaseRepository
{

    public function paginate(array $attributes, callable $sortCallback): Paginator
    {
        return $this->getModelClass()::search($attributes['search'] ?? null)
                    ->when($attributes['sort'] ?? null, function ($query) use ($attributes, $sortCallback) {
                        $sortCallback($query);
                    })
                    ->paginate($attributes['size'] ?? 10, 'page', $attributes['page'] ?? 1);
    }

    protected function getModelClass(): string
    {
        return $this->model;
    }
}
