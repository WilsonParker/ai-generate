<?php

namespace App\Http\Repositories\Stock;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use AIGenerate\Models\Stock\Enums\Status;
use AIGenerate\Models\Stock\StockCount;

class StockSimilarSearchRepository extends StockRepository
{
    public function index(array $attributes, callable $sortCallback): Paginator
    {
        $paginator = StockCount::with(['stock.information'])
                               ->whereHas('stock', function (Builder $query) use ($attributes) {
                                   $query->where('stock_status_code', Status::Enabled->value)
                                         ->when(isset($attributes['search']), fn($query) => $query->where(function (Builder $subQuery) use ($attributes) {
                                             $subQuery->whereFullText('title', $attributes['search'])->orWhereFullText('description', $attributes['search']);
                                         }))
                                         ->when(isset($attributes['gender']), fn($query) => $query->whereHas('information', fn(
                                             $subQuery) => $subQuery->where('gender', $attributes['gender'])))
                                         ->when(isset($attributes['ethnicity']), fn($query) => $query->whereHas('information', fn(
                                             $subQuery) => $subQuery->where('ethnicity', $attributes['ethnicity'])));
                               })
                               ->addSelect(['*', 'generates as top', 'views as hottest'])
                               ->when(isset($attributes['sort']), $sortCallback);

        if ($attributes['need_total'] ?? false) {
            return $paginator->paginate($attributes['per'], ['*'], 'page', $attributes['page']);
        }

        return $paginator->simplePaginate($attributes['per'], ['*'], 'page', $attributes['page']);
    }
}
