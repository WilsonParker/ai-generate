<?php

namespace App\Http\Repositories\Stock;

use Illuminate\Contracts\Pagination\Paginator;
use AIGenerate\Models\Stock\Enums\Status;

class StockMatchSearchRepository extends StockRepository
{
    public function index(array $attributes, callable $sortCallback): Paginator
    {
        return $this->model::with([
            'information',
            'count',
        ])
                           ->join('stock_count', 'stocks.id', '=', 'stock_count.stock_id')
                           ->withAggregate('count', 'generates')
                           ->withAggregate('count', 'views')
                           ->addSelect([
                               'count_generates as top',
                               'count_views as hottest',
                           ])
                           ->when(isset($attributes['search']), fn($query) => $query->whereRaw("WHERE MATCH(title, description) AGAINST('girl')"))
                           ->when(isset($attributes['sort']), $sortCallback)
                           ->when(isset($attributes['gender']), fn($query) => $query->whereHas('information', fn($subQuery) => $subQuery->where('gender', $attributes['gender'])))
                           ->when(isset($attributes['ethnicity']), fn($query) => $query->whereHas('information', fn(
                               $subQuery) => $subQuery->where('ethnicity', $attributes['ethnicity'])))
                           ->where('stock_status_code', Status::Enabled->value)
                           ->paginate($attributes['per']);
    }
}
