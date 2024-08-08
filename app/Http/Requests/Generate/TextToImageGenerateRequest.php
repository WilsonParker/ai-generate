<?php

namespace App\Http\Requests\Generate;

use App\Http\Requests\BaseRequest;
use AIGenerate\Services\Generate\Enums\SamplingMethod;

class TextToImageGenerateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'prompt'           => [
                'required',
                'string',
            ],
            'negative'         => [
                'nullable',
                'string',
            ],
            'width'            => [
                'nullable',
                'int',
                'min:128',
                'max:2048',
            ],
            'height'           => [
                'nullable',
                'int',
                'min:128',
                'max:2048',
            ],
            'sampling_method'  => [
                'nullable',
                'string',
                'in:' . implode(',', SamplingMethod::getEnumAttributeValues()->toArray()),
            ],
            'steps'            => [
                'nullable',
                'int',
                'min:1',
                'max:100',
            ],
            'seed'             => [
                'nullable',
                'int',
            ],
            'cfg_scale'        => [
                'nullable',
                'numeric',
                'min:1',
                'max:30',
            ],
            'alwayson_scripts' => [
                'nullable',
                'json',
            ],
        ];
    }

}
