<?php

namespace App\Console\Commands;

use AIGenerate\Services\Cache\Traits\CacheKeyTraits;
use AIGenerate\Services\Stock\Sorts\Enums\Sorts;
use AIGenerate\Services\Stock\Sorts\HottestSort;
use AIGenerate\Services\Stock\Sorts\NewestSort;
use AIGenerate\Services\Stock\Sorts\TopSort;
use AIGenerate\Services\Stock\StockService;
use App\Http\Repositories\Stock\StockFilterRepository;
use App\Http\Repositories\Stock\StockSimilarSearchRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class StockIndexCacheCommand extends Command
{
    use CacheKeyTraits;

    protected $signature = 'stock:index-cache';

    protected $description = '메인 스톡 목록의 캐시를 재설정 합니다';

    public function handle(): void
    {
        $service = new StockService(
            app()->make(StockFilterRepository::class),
            app()->make(StockSimilarSearchRepository::class),
            new \AIGenerate\Services\Stock\Sorts\Sorts([
                app()->make(HottestSort::class),
                app()->make(TopSort::class),
                app()->make(NewestSort::class),
            ]),
        );
        $sort = Sorts::TOP;
        $per = 99;
        for ($page = 1; $page <= 10; $page++) {
            Cache::put($this->makeCacheKey([
                'stock',
                'index',
                $sort->value,
                $per,
                $page,
            ]), $service->index(null, $sort, $page, null, null, $per), 60 * 60 * 1);
        }
    }
}
