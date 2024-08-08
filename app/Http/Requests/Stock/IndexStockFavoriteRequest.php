<?php

namespace App\Http\Requests\Stock;

use App\Http\Requests\BaseRequest;

class IndexStockFavoriteRequest extends BaseRequest
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
