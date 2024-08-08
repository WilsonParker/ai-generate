<?php

namespace App\Http\Requests\Generate;

use App\Http\Requests\BaseRequest;
use OpenApi\Annotations as OA;
use AIGenerate\Services\Generate\Enums\SamplingMethod;

/**
 * @OA\Schema(
 *   schema="ImageToImageGenerateRequest",
 *   required={"image", "prompt"},
 *   @OA\Property(property="image", description="image", type="file"),
 *   @OA\Property(property="prompt", type="string", description="prompt", example="a yellow sports car"),
 *   @OA\Property(property="fill_prompt", type="boolean", description="fill prompt", example="true"),
 *   @OA\Property(property="negative", type="string", description="negative", example="nsfw"),
 *   @OA\Property(property="fill_negative", type="boolean", description="fill negative", example="true"),
 *   @OA\Property(property="width", type="number", description="width", example="512"),
 *   @OA\Property(property="height", type="number", description="height", example="512"),
 *   @OA\Property(property="sampling_method", type="string", description="sampling_method", example="DPM++ 2M SDE KARRAS"),
 *   @OA\Property(property="steps", type="number", description="steps", example="20"),
 *   @OA\Property(property="seed", type="number", description="seed", example="-1"),
 *   @OA\Property(property="cfg_scale", type="number", description="cfg_scale", example="7"),
 *   @OA\Property(property="denoising_strength", type="number", description="denoising_strength", example="0.75"),
 *   @OA\Property(property="alwayson_scripts", type="array", description="alwayson_scripts", @OA\Items(type="string")),
 * )
 */
class ImageToImageGenerateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'image'              => [
                'required',
                'image',
                'max:32768',
            ],
            'prompt'             => [
                'required',
                'string',
            ],
            'fill_prompt'        => [
                'nullable',
                'boolean',
            ],
            'negative'           => [
                'nullable',
                'string',
            ],
            'fill_negative'      => [
                'nullable',
                'boolean',
            ],
            'width'              => [
                'nullable',
                'int',
                'min:128',
                'max:2048',
            ],
            'height'             => [
                'nullable',
                'int',
                'min:128',
                'max:2048',
            ],
            'sampling_method'    => [
                'nullable',
                'string',
                'in:' . implode(',', SamplingMethod::getEnumAttributeValues()->toArray()),
            ],
            'steps'              => [
                'nullable',
                'int',
                'min:1',
                'max:100',
            ],
            'seed'               => [
                'nullable',
                'int',
            ],
            'cfg_scale'          => [
                'nullable',
                'integer',
                'min:1',
                'max:30',
            ],
            'denoising_strength' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1',
            ],
            'alwayson_scripts'   => [
                'nullable',
                'json',
            ],
        ];
    }

}
