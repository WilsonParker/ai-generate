<?php

namespace App\Http\Requests\Prompt;

use App\Http\Requests\BaseRequest;
use AIGenerate\Services\AI\OpenAI\Enums\OpenAITypes;

class PreviewPromptRequest extends BaseRequest
{
    public function rules(): array
    {
        $prompt = $this->route('prompt');
        $rule = 'string';
        if ($prompt->prompt_type_code == OpenAITypes::Completion->value) {
            $rule = 'json';
        }
        return [
            'guide'    => [
                'required',
                'string',
            ],
            'order'    => [
                'nullable',
                'string',
            ],
            'template' => [
                'required',
                $rule,
            ],
        ];
    }

}
