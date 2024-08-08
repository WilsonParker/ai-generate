<?php

namespace App\Models\Generate;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TextGenerateExport extends \AIGenerate\Models\Generate\TextGenerateExport
{
    public function generate(): BelongsTo
    {
        return $this->belongsTo(TextGenerate::class, 'text_generate_id', 'id');
    }
}
