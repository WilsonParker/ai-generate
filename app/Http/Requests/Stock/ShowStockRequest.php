<?php

namespace App\Http\Requests\Stock;

use App\Http\Requests\BaseRequest;

class ShowStockRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => [
                'nullable',
                'integer',
            ],
        ];
    }
}
