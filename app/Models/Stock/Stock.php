<?php

namespace App\Models\Stock;

use AIGenerate\Services\Stock\Contracts\StockContract;
use App\Models\Stock\AI\StockGenerateInformation;
use App\Services\Image\Models\Media;
use App\Services\SiteMap\Url;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Spatie\Sitemap\Contracts\Sitemapable;

class Stock extends \AIGenerate\Models\Stock\Stock implements Sitemapable, StockContract, Feedable
{
    public static function getFeedItems()
    {
        $except = collect([
            'nude',
            'sex',
            'adult',
            'naked',
            'porn',
            'xxx',
            'pussy',
            'dick',
            'fuck',
            'vagina',
            'penis',
            'boob',
            'breast',
            'ass',
        ]);
        $containsQuery = function ($query, $key, $value) {
            $query->where($key, 'not like', "%$value%");
        };
        return Cache::remember('rss_stock', 60 * 60 * 24, fn() => Stock::withAggregate('count', 'views')
                                                                       ->enabled()
                                                                       ->isNotRssExported()
                                                                       ->where(function ($query) use ($containsQuery, $except) {
                                                                           $except->each(fn($item) => $containsQuery($query, 'title', $item));
                                                                           $except->each(fn($item) => $containsQuery($query, 'description', $item));
                                                                       })
                                                                       ->orderBy('count_views', 'desc')
                                                                       ->limit(200)
                                                                       ->get());
    }

    public function scopeIsNotRssExported(Builder $query): void
    {
        $query->where('is_rss_exported', false);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'gallery', 'model_type', 'model_id', 'id');
    }

    public function generateInformation(): HasOne
    {
        return $this->hasOne(StockGenerateInformation::class, 'stock_id', 'id');
    }

    public function toSitemapTag(): Url|string|array
    {
        $url = Url::create(config('constant.sitemap') . '/ai-stock-image/detail/' . $this->keyword . '-' . $this->getKey());
        if ($this->images) {
            $url->addImage($this->images->first()->getUrl('gallery-thumbnail'));
        }
        return $url;
    }

    public function getDetailImage(): \Spatie\MediaLibrary\MediaCollections\Models\Media
    {
        return $this->detailImage;
    }

    public function getCropImageUrl(): string
    {
        return $this->origin->images->first()->getUrl('gallery-crop');
    }

    public function getEthnicity(): string
    {
        return $this->generateInformation->race ?? $this->information->ethnicity;
    }

    public function getGender(): string
    {
        return $this->generateInformation->gender ?? $this->information->gender;
    }

    public function getGenerateInformation(): Model
    {
        return $this->generateInformation;
    }

    /**
     * Get the name of the index associated with the model.
     */
    public function searchableAs(): string
    {
        return 'stock_index';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        // Customize the data array...
        return array_merge(
            $this->only([
                'title',
                'description',
                'created_at',
                'updated_at',
            ]),
            [
                'ethnicity' => $this->information->ethnicity,
                'gender'    => $this->information->gender,
                'hottest'   => $this->count ? $this->count->views : 0,
                'top'       => $this->count ? $this->count->generates : 0,
            ],
        );
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->isEnabled();
    }

    public function toFeedItem(): FeedItem
    {
        $image = $this->detailImage?->getUrl('gallery-thumbnail');
        return FeedItem::create()
                       ->id($this->id)
                       ->title($this->title)
                       ->summary("<img src='$image'/>" . $this->description)
                       ->updated($this->updated_at)
                       ->link('https://ai-generate/ai-stock-image/detail/' . $this->keyword . '-' . $this->id)
                       ->authorName('AI Generate')
                       ->authorEmail('contact@gmail.com');
    }
}
