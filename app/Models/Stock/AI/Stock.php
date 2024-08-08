<?php

namespace App\Models\Stock\AI;

use AIGenerate\Models\BaseModel;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Stock extends BaseModel implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'stocks';
    protected $connection = 'ai';

    public function images(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(StockImage::class, 'gallery', 'model_type', 'model_id', 'id');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $props = $media?->getAttribute('custom_properties');
        $width = $props['width'];
        $height = $props['height'] - 35;

        $this->addMediaConversion('gallery-crop')
             ->crop(Manipulations::CROP_TOP, $width, $height)
             ->keepOriginalImageFormat()
             ->width($props['rWidth'])
             ->height($props['rHeight']);
    }
}
