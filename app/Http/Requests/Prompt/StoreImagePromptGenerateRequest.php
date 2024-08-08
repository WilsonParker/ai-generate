<?php

namespace App\Http\Requests\Prompt;

use Illuminate\Support\Arr;
use AIGenerate\Services\AI\OpenAI\Images\Request;

class StoreImagePromptGenerateRequest extends Request
{
    public function rules(): array
    {
        $prompt = $this->route('prompt');
        return array_merge(
            $prompt->options->mapWithKeys(function ($option) {
                return [
                    "p{$option->getKey()}" => [
                        'nullable',
                        'string',
                    ],
                ];
            })->toArray(),
            Arr::except(parent::rules(), ['prompt']),
        );
    }

}
