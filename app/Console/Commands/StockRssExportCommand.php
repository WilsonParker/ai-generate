<?php

namespace App\Console\Commands;

use App\Models\Stock\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class StockRssExportCommand extends Command
{
    protected $signature = 'rss:stock';

    protected $description = 'rss stock export';

    public function handle(): void
    {
        Stock::getFeedItems()->each(function ($item) {
            $stock = Stock::find($item->id);
            $stock->is_rss_exported = true;
            $stock->save();
        });
        Cache::forget('rss_stock');
    }
}
