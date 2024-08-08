<?php

namespace App\Http\Repositories\Stock;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use AIGenerate\Services\Cache\Traits\CacheKeyTraits;

class StockSimilarSearchWithCacheRepository extends StockSimilarSearchRepository
{
    use CacheKeyTraits;

    private int $cacheTime =
        /* second */
        60 *
        /* minute */
        60 *
        /* hour */
        6;

    public function index(array $attributes, callable $sortCallback): Paginator
    {
        return Cache::remember($this->makeCacheKey(
            [
                'stock',
                'index',
                $attributes['search'],
                $attributes['sort']?->value,
                $attributes['gender'],
                $attributes['ethnicity'],
                $attributes['per'],
                $attributes['page'],
            ]), $this->cacheTime,
            function () use ($attributes, $sortCallback) {
                $countKey = $this->makeCacheKey([
                    'stock',
                    'total',
                    $attributes['search'],
                    $attributes['gender'],
                    $attributes['ethnicity'],
                ]);
                $attributes['need_total'] = !Cache::has($countKey);
                $paginator = parent::index($attributes, $sortCallback);
                if ($attributes['need_total']) {
                    Cache::put($countKey, $paginator->total(), $this->cacheTime);
                    $paginator->count = $paginator->total();
                    $paginator->end = $paginator->lastPage();
                } else {
                    $paginator->count = Cache::get($countKey);
                    $paginator->end = $paginator->count / $paginator->perPage();
                    $round = round($paginator->end, 0, PHP_ROUND_HALF_DOWN);
                    if ($paginator->end > $round) {
                        $paginator->end = $round + 1;
                    } else {
                        $paginator->end = $round;
                    }
                }
                return $paginator;
            });
    }

}
