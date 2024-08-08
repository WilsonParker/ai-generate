<?php

namespace App\Http\Requests\Prompt;

use App\Http\Requests\BaseRequest;
use App\Services\Prompt\Sorts\Enums\Sorts;

class IndexPromptFavoriteRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
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
        ];
    }

}
