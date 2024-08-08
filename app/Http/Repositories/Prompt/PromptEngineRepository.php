<?php

namespace App\Http\Repositories\Prompt;

use App\Http\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use AIGenerate\Services\AI\OpenAI\Enums\OpenAITypes;

class PromptEngineRepository extends BaseRepository
{
    public function getEngineByType(OpenAITypes $type): Collection
    {
        return $this->model::where('prompt_type_code', $type->value)->get();
    }
}
