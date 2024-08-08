<?php

namespace App\Models\Generate;

use App\Models\User\User;
use App\Services\Image\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ImageToImageGenerate extends \AIGenerate\Models\Generate\ImageToImageGenerate
{

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function originImage(): MorphMany
    {
        return $this->images()->where('collection_name', 'origin');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'gallery', 'model_type', 'model_id', 'id');
    }

    public function galleryImage(): MorphMany
    {
        return $this->images()->where('collection_name', 'gallery');
    }
}
