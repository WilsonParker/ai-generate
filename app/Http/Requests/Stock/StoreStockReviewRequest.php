<?php

namespace App\Http\Requests\Stock;

use App\Http\Requests\BaseRequest;
use AIGenerate\Models\Stock\Enums\ReviewTypes;
use AIGenerate\Models\Stock\StockReviewType;

class StoreStockReviewRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'exists:' . StockReviewType::class . ',code',
            ],
            'memo' => [
                $this->input('type') === ReviewTypes::from('feedback')->value ? 'required' : 'nullable',
                'string',
                'min:1',
                'max:1024',
            ],
        ];
    }
}
