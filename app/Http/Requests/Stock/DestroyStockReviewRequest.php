<?php

namespace App\Http\Requests\Stock;

use App\Http\Requests\BaseRequest;
use AIGenerate\Models\Stock\StockReviewType;

class DestroyStockReviewRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'exists:' . StockReviewType::class . ',code',
            ],
        ];
    }
}
