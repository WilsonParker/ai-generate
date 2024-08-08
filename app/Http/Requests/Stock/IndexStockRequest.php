<?php

namespace App\Http\Requests\Stock;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use AIGenerate\Models\Stock\StockFilter;
use AIGenerate\Services\Stock\Sorts\Enums\Sorts;

class IndexStockRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'    => [
                'nullable',
                'string',
            ],
            'gender'    => [
                'nullable',
                'string',
                Rule::exists(StockFilter::class, 'code')->where(function ($query) {
                    return $query->where('parent', 'gender');
                }),
            ],
            'ethnicity' => [
                'nullable',
                'string',
                Rule::exists(StockFilter::class, 'code')->where(function ($query) {
                    return $query->where('parent', 'ethnicity');
                }),
            ],
            'sort'      => [
                'nullable',
                'string',
                'in:' . collect(Sorts::cases())->map(fn($case) => $case->value)->implode(','),
            ],
            'page'      => [
                'nullable',
                'integer',
            ],
            'per'       => [
                'nullable',
                'integer',
                'max:99',
            ],
        ];
    }

}
