<?php

namespace App\Http\Repositories\Stock;

use App\Http\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class StockFilterRepository extends BaseRepository
{
    public function getFilterByCode(string $code): Collection
    {
        return $this->model::where('parent', $code)->get();
    }
}
