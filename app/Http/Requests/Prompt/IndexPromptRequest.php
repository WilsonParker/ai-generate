<?php

namespace App\Http\Requests\Prompt;

use App\Http\Requests\BaseRequest;
use App\Services\Prompt\Sorts\Enums\Sorts;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\Prompt\PromptCategory;
use AIGenerate\Models\Prompt\PromptType;

class IndexPromptRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'       => [
                'nullable',
                'string',
            ],
            'categories'   => [
                'nullable',
                'array',
            ],
            'categories.*' => [
                'nullable',
                'string',
                'exists:' . PromptCategory::class . ',id',
            ],
            'types'        => [
                'nullable',
                'array',
            ],
            'types.*'      => [
                'nullable',
                'string',
                'exists:' . PromptType::class . ',code',
            ],
            'sort'         => [
                'nullable',
                'string',
                'in:' . collect(Sorts::cases())->map(fn($case) => $case->value)->implode(','),
            ],
            'page'         => [
                'nullable',
                'integer',
            ],
            'size'         => [
                'nullable',
                'integer',
            ],
            'last'         => [
                'nullable',
                'integer',
                'exists:' . Prompt::class . ',id',
            ],
        ];
    }

}
