<?php

namespace App\Observers\Stock;

use AIGenerate\Models\Stock\StockView;

class StockViewObserver
{
    public function updated(StockView $stockView): void
    {
        $stockView->stock->count->increment('views');
    }
}
