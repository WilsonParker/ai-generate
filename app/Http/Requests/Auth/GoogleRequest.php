<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class GoogleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'redirect' => [
                'nullable',
                'string'
            ]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
