<?php

namespace App\Http\Requests\Prompt;

use Illuminate\Support\Arr;
use AIGenerate\Services\AI\OpenAI\Completion\Request;

class StoreCompletionPromptGenerateRequest extends Request
{
    public function rules(): array
    {
        $prompt = $this->route('prompt');
        return array_merge(
            $prompt->fillableOptions->mapWithKeys(function ($option) {
                return [
                    "p{$option->getKey()}" => [
                        'nullable',
                        'string',
                    ],
                ];
            })->toArray(),
            $this->commonRules(),
            Arr::except(parent::rules(), ['prompt', 'model']),
        );
    }
}
