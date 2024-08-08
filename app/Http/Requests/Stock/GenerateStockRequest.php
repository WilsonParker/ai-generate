<?php

namespace App\Http\Requests\Stock;

use App\Http\Requests\BaseRequest;
use AIGenerate\Models\Stock\Enums\Ethnicity;
use AIGenerate\Models\Stock\Enums\Gender;

class GenerateStockRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ethnicity'      => [
                'nullable',
                'in:' . collect(Ethnicity::cases())->map(fn($item) => $item->value)->implode(','),
            ],
            'gender'         => [
                'nullable',
                'in:' . collect(Gender::cases())->map(fn($item) => $item->value)->implode(','),
            ],
            'age'            => [
                'nullable',
                'numeric',
                'min:5',
                'max:95',
            ],
            'skin_reality'   => [
                'nullable',
                'boolean',
            ],
            'pose_variation' => [
                'nullable',
                'boolean',
            ],
        ];
    }

}
