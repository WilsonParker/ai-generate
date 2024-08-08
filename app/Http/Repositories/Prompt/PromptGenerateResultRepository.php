<?php

namespace App\Http\Repositories\Prompt;

use App\Http\Repositories\BaseRepository;
use Carbon\Carbon;
use AIGenerate\Models\Prompt\PromptGenerate;
use AIGenerate\Models\Prompt\PromptGenerateResult;

class PromptGenerateResultRepository extends BaseRepository
{
    public function createForPromptGenerate(
        PromptGenerate $promptGenerate,
        string $url,
        Carbon $created,
    ): PromptGenerateResult {
        return $promptGenerate->results()->create([
            'result'     => $url,
            'created_at' => $created,
        ]);
    }
}
