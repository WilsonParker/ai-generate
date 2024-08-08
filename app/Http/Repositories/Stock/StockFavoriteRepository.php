<?php

namespace App\Http\Repositories\Stock;

use App\Http\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\Paginator;
use AIGenerate\Models\Stock\Stock;
use AIGenerate\Models\Stock\StockFavorite;
use AIGenerate\Models\User\User;

class StockFavoriteRepository extends BaseRepository
{
    public function index(User $user, int $page = 1): Paginator
    {
        return $user->stockFavorites()
                    ->orderBy('id', 'desc')
                    ->paginate(12, ['*'], 'page', $page);
    }

    public function isExistsForModel(Stock $stock, User $user): bool
    {
        return $this->model::where('stock_id', $stock->id)
                           ->where('user_id', $user->id)
                           ->exists();
    }

    public function store(Stock $stock, User $user): StockFavorite
    {
        return $this->model::firstOrCreate([
            'stock_id' => $stock->id,
            'user_id'  => $user->id,
        ]);
    }

    public function destroy(Stock $stock, User $user): ?bool
    {
        $result = $this->model::where('stock_id', $stock->id)
                              ->where('user_id', $user->id)
                              ->firstOrFail();

        return $result->delete();
    }
}
