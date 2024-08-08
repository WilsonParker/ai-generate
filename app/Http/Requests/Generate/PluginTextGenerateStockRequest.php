<?php

namespace App\Http\Requests\Generate;

class PluginTextGenerateStockRequest extends TextGenerateStockRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            ...parent::rules(),
            'hook_key' => [
                'nullable',
                'string',
            ],
        ];
    }

}
