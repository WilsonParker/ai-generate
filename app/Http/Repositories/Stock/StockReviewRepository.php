<?php

namespace App\Http\Repositories\Stock;

use App\Http\Repositories\BaseRepository;
use AIGenerate\Models\Stock\Enums\ReviewTypes;
use AIGenerate\Models\Stock\Stock;
use AIGenerate\Models\Stock\StockReview;
use AIGenerate\Models\User\User;

class StockReviewRepository extends BaseRepository
{
    public function isExistsForModel(Stock $stock, User $user, string $type): bool
    {
        return $this->model::where('stock_id', $stock->id)
                           ->where('user_id', $user->id)
                           ->where('type', $type)
                           ->exists();
    }

    public function store(Stock $stock, User $user, array $attributes = []): StockReview
    {
        return $this->model::firstOrCreate([
            'stock_id' => $stock->id,
            'user_id'  => $user->id,
            'type'     => $attributes['type'],
            'memo'     => $attributes['memo'] ?? null,
        ]);
    }

    public function destroy(Stock $stock, User $user, string $type): ?bool
    {
        $result = $this->model::where('stock_id', $stock->id)
                              ->where('user_id', $user->id)
                              ->where('type', $type)
                              ->firstOrFail();

        return $result->delete();
    }
}
