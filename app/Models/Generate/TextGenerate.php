<?php

namespace App\Models\Generate;

use App\Models\User\User;
use App\Services\Image\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TextGenerate extends \AIGenerate\Models\Generate\TextGenerate
{

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'gallery', 'model_type', 'model_id', 'id');
    }
}
