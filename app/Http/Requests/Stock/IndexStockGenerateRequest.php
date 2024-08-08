<?php

namespace App\Http\Requests\Stock;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use AIGenerate\Models\Stock\StockFilter;
use AIGenerate\Services\Stock\Sorts\Enums\Sorts;

class IndexStockGenerateRequest extends BaseRequest
{
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
