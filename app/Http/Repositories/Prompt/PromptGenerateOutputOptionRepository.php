<?php

namespace App\Http\Repositories\Prompt;

use App\Http\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use AIGenerate\Models\Prompt\PromptGenerateLanguage;
use AIGenerate\Models\Prompt\PromptGenerateTone;
use AIGenerate\Models\Prompt\PromptGenerateWritingStyle;

class PromptGenerateOutputOptionRepository extends BaseRepository
{
    public function getLanguages(): Collection
    {
        return PromptGenerateLanguage::all();
    }

    public function getTones(): Collection
    {
        return PromptGenerateTone::all();
    }

    public function getWritingStyles(): Collection
    {
        return PromptGenerateWritingStyle::all();
    }

}
