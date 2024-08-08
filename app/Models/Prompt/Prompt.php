<?php

namespace App\Models\Prompt;

use App\Services\Image\Models\Image;
use App\Services\SiteMap\Url;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use AIGenerate\Models\Prompt\Enums\Status;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sitemap\Contracts\Sitemapable;

class Prompt extends \AIGenerate\Models\Prompt\Prompt implements Sitemapable
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        /*static::addGlobalScope('enabled', function (Builder $builder) {
            $builder->where('prompt_status_code', Status::Enabled->value);
        });*/
    }

    public function scopeEnabled(Builder $query): void
    {
        $query->where('prompt_status_code', Status::Enabled->value);
    }

    public function imageModels(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function toSitemapTag(): Url|string|array
    {
        $url = Url::create(config('constant.sitemap') . "/prompt/detail/{$this->getKey()}");
        if ($this->images) {
            $this->images->each(function (Media $image) use ($url) {
                $url->addImage($image->getUrl('gallery-thumbnail'));
            });
        }
        return $url;
    }

    public function getWith(): array
    {
        return $this->with;
    }
}
