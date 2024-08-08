<?php

namespace App\Http\Requests\Generate;

use App\Http\Requests\BaseRequest;

class IndexGenerateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
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
