<?php

namespace App\Http\Requests\Prompt;

use App\Http\Requests\BaseRequest;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;
use AIGenerate\Models\Prompt\PromptEngine;
use AIGenerate\Services\AI\OpenAI\Completion\ApiService;
use AIGenerate\Services\AI\OpenAI\Completion\Request;
use AIGenerate\Services\AI\OpenAI\Enums\OpenAITypes;

class UpdatePromptTemplateRequest extends BaseRequest
{
    public function rules(): array
    {
        $prompt = $this->route('prompt');
        $rule = 'string';
        $additional = [];
        if ($prompt->prompt_type_code == OpenAITypes::Completion->value) {
            $rule = 'json';
        } else if ($prompt->prompt_type_code == OpenAITypes::Chat->value) {
            $additional = [
                'engine' => [
                    'required',
                    Rule::exists(PromptEngine::class, 'code')->where(function (Builder $query) use ($prompt) {
                        return $query->where('prompt_type_code', $prompt->prompt_type_code);
                    }),
                ],
            ];
        }
        return array_merge([
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
        ], $additional);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $prompt = $this->route('prompt');
            if ($prompt->prompt_type_code == OpenAITypes::Completion->value) {
                $apiService = app()->make(ApiService::class);
                $apiService->validate(new Request, get_object_vars(json_decode($this->template)));
            }
        });
    }
}
