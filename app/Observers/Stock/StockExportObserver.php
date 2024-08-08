<?php

namespace App\Observers\Stock;

use AIGenerate\Models\Stock\StockExport;

class StockExportObserver
{
    public function created(StockExport $stockExport): void
    {
        $stockExport->generate->user->count->increment('exports');
    }
}
