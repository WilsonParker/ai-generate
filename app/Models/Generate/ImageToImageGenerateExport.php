<?php

namespace App\Models\Generate;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageToImageGenerateExport extends \AIGenerate\Models\Generate\ImageToImageGenerateExport
{
    public function generate(): BelongsTo
    {
        return $this->belongsTo(ImageToImageGenerate::class, 'image_to_image_generate_id', 'id');
    }
}
