<?php

namespace App\Http\Repositories\Stock;

use App\Http\Repositories\BaseRepository;
use AIGenerate\Models\Stock\Stock;
use AIGenerate\Models\Stock\StockLike;
use AIGenerate\Models\User\User;

class StockLikeRepository extends BaseRepository
{
    public function isExistsForModel(Stock $stock, User $user): bool
    {
        return $this->model::where('stock_id', $stock->id)
                           ->where('user_id', $user->id)
                           ->exists();
    }

    public function store(Stock $stock, User $user): StockLike
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
