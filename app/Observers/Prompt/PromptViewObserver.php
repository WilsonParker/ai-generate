<?php

namespace App\Observers\Prompt;

use AIGenerate\Models\Prompt\PromptView;

class PromptViewObserver
{
    public function updated(PromptView $promptView): void
    {
        $promptView->prompt->count->increment('views');
    }
}
