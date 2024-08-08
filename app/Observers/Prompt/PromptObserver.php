<?php

namespace App\Observers\Prompt;

use AIGenerate\Models\Prompt\Prompt;

class PromptObserver
{
    public function created(Prompt $prompt): void
    {
        $prompt->count()->create();
    }
}
