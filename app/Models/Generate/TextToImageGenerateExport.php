<?php

namespace App\Models\Generate;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TextToImageGenerateExport extends \AIGenerate\Models\Generate\TextToImageGenerateExport
{
    public function generate(): BelongsTo
    {
        return $this->belongsTo(TextToImageGenerate::class, 'text_to_image_generate_id', 'id');
    }
}
