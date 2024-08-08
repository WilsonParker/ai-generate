<?php

namespace App\Observers\Stock;

use AIGenerate\Models\Stock\StockReview;

class StockReviewObserver
{
    public function created(StockReview $stockReview): void
    {
        $stockReview->stock->count->increment($stockReview->type);
    }

    public function deleted(StockReview $stockReview): void
    {
        $stockReview->stock->count->decrement($stockReview->type);
    }
}
