<?php

namespace App\Observers\Stock;

use AIGenerate\Models\Stock\StockLike;

class StockLikeObserver
{
    public function created(StockLike $stockLike): void
    {
        $stockLike->stock->count->increment('likes');
    }

    public function deleted(StockLike $stockLike): void
    {
        $stockLike->stock->count->decrement('likes');
    }
}
