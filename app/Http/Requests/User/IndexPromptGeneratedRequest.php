<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use App\Services\Prompt\Sorts\Enums\Sorts;

class IndexPromptGeneratedRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'search' => [
                'nullable',
                'string',
            ],
            'sort' => [
                'nullable',
                'string',
                'in:' . collect(Sorts::cases())->map(fn($case) => $case->value)->implode(',')
            ],
            'page' => [
                'nullable',
                'integer',
            ],
            'size' => [
                'nullable',
                'integer',
            ],
            'start' => [
                'nullable',
                'before:end',
                'date',
            ],
            'end' => [
                'nullable',
                'after:start',
                'date',
            ],
        ];
    }

}
