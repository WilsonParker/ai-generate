<?php

namespace App\Http\Repositories\Prompt;

use App\Http\Repositories\BaseRepository;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\Prompt\PromptOption;

class PromptOptionRepository extends BaseRepository
{
    public function store(Prompt $prompt, string $name, ?string $value): PromptOption
    {
        return $prompt->options()->create([
            'name'  => $name,
            'value' => $value,
        ]);
    }
}
