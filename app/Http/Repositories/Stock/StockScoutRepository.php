<?php

namespace App\Http\Repositories\Stock;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use AIGenerate\Models\Stock\Stock;
use AIGenerate\Models\User\User;
use AIGenerate\Services\Stock\Contracts\StockRepositoryContract;

class StockScoutRepository extends StockRepository implements StockRepositoryContract
{
    public function index(array $attributes, callable $sortCallback): Paginator
    {
        return $this->model::search($attributes['search'] ?? null)
                           ->when(isset($attributes['sort']), $sortCallback)
                           ->when(isset($attributes['gender']), fn($query) => $query->where('gender', $attributes['gender']))
                           ->when(isset($attributes['ethnicity']), fn($query) => $query->where('ethnicity', $attributes['ethnicity']))
                           ->paginate($attributes['per'], 'page', $attributes['page']);
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

}
