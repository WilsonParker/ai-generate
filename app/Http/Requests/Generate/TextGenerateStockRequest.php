<?php

namespace App\Http\Requests\Generate;

use App\Http\Requests\BaseRequest;
use AIGenerate\Models\Stock\Enums\Ethnicity;
use AIGenerate\Models\Stock\Enums\Gender;
use AIGenerate\Services\Generate\Enums\TextGenerateType;

class TextGenerateStockRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'prompt'       => [
                'required',
                'string',
                'min:10',
            ],
            'width'        => [
                'required',
                'numeric',
                'min:256',
                'max:1024',
            ],
            'height'       => [
                'required',
                'numeric',
                'min:256',
                'max:1024',
            ],
            /*'ratio' => [
                'required',
                'in:' . collect(Ratio::cases())->map(fn($item) => $item->value)->implode(','),
            ],*/
            'type'         => [
                'required',
                'in:' . collect(TextGenerateType::cases())->map(fn($item) => $item->value)->implode(','),
            ],
            'ethnicity'    => [
                // 'required_if:type,' . TextGenerateType::Portrait->value,
                'nullable',
                'in:' . collect(Ethnicity::cases())->map(fn($item) => $item->value)->implode(','),
            ],
            'gender'       => [
                // 'required_if:type,' . TextGenerateType::Portrait->value,
                'nullable',
                'in:' . collect(Gender::cases())->map(fn($item) => $item->value)->implode(','),
            ],
            'age'          => [
                // 'required_if:type,' . TextGenerateType::Portrait->value,
                'nullable',
                'numeric',
                'min:5',
                'max:95',
            ],
            'skin_reality' => [
                'nullable',
                'boolean',
            ],
        ];
    }

}
