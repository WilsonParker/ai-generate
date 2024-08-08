<?php

namespace App\Http\Repositories\Stock;

use App\Http\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use AIGenerate\Models\Stock\Enums\Status;
use AIGenerate\Models\Stock\Stock;
use AIGenerate\Models\User\User;
use AIGenerate\Services\Stock\Contracts\StockRepositoryContract;

class StockRepository extends BaseRepository implements StockRepositoryContract
{
    public function index(array $attributes, callable $sortCallback): Paginator
    {
        return $this->model::with([
            'information',
            'count',
        ])
                           ->join('stock_count', 'stocks.id', '=', 'stock_count.stock_id')
                           ->addSelect([
                               'stocks.*',
                               'stock_count.generates as top',
                               'stock_count.views as hottest',
                           ])
                           ->when(isset($attributes['search']), fn($query) => $query->where(function ($subQuery) use ($attributes) {
                               $subQuery->where('title', 'like', "%{$attributes['search']}%")
                                        ->orWhere('description', 'like', "%{$attributes['search']}%");
                           }))
                           ->when(isset($attributes['sort']), $sortCallback)
                           ->when(isset($attributes['gender']), fn($query) => $query->whereHas('information', fn($subQuery,
                           ) => $subQuery->where('gender', $attributes['gender'])))
                           ->when(isset($attributes['ethnicity']), fn($query) => $query->whereHas('information', fn($subQuery,
                           ) => $subQuery->where('ethnicity', $attributes['ethnicity'])))
                           ->where('stock_status_code', Status::Enabled->value)
                           ->paginate($attributes['per']);
    }

    public function isLike(Stock $stock, User $user): bool
    {
        return $user->likes()->where('stock_id', $stock->getKey())->exists();
    }

    public function isFavorite(Stock $stock, User $user): bool
    {
        return $user->stockFavorites()->where('stock_id', $stock->getKey())->exists();
    }

    public function hasReview(Stock $stock, User $user): Collection
    {
        return $user->stockReviews()->where('stock_id', $stock->getKey())->get();
    }

    public function similar(array $attributes, callable $sortCallback): Paginator
    {
        return $this->index($attributes, $sortCallback);
    }
}
