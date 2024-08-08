<?php

namespace App\Services\Image\Traits;

use App\Services\Image\Contracts\ImageModel;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasDefaultThumbnailTrait
{
    function getThumbnail(ImageModel|Media|string|null $image, ?string $default = null): string
    {
        if (isset($image)) {
            if ($image instanceof ImageModel) {
                return $image->getLink('gallery-thumbnail');
            } else {
                return $image;
            }
        } else {
            return $this->getDefaultThumbnail($default);
        }
    }

    function getDefaultThumbnail(?string $default)
    {
        return $default ?? config('constant.images.default');
    }
}
