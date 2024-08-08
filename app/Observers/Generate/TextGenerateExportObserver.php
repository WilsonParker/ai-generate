<?php

namespace App\Observers\Generate;

use App\Models\Generate\TextGenerateExport;

class TextGenerateExportObserver
{
    public function created(TextGenerateExport $textGenerateExport): void
    {
        $textGenerateExport->generate->user->count->increment('text_generate_exports');
    }
}
