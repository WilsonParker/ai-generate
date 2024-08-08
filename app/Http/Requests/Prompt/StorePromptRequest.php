<?php

namespace App\Http\Requests\Prompt;

use App\Http\Requests\BaseRequest;
use OpenApi\Annotations as OA;
use AIGenerate\Models\Prompt\PromptCategory;
use AIGenerate\Models\Prompt\PromptType;


/**
 * @OA\Schema(
 *   schema="StorePromptRequest",
 *   required={"type", "category", "title", "description", "price"},
 *   @OA\Property(property="type", type="string", description="api type", example="image"),
 *   @OA\Property(property="category", type="string", description="prompt category", example="1"),
 *   @OA\Property(property="title", type="string", description="prompt title", example="title"),
 *   @OA\Property(property="description", description="prompt description", type="string", example="description"),
 *   @OA\Property(property="price", description="prompt price per generate, $dollor", type="integer", example="1", minimum=0 ),
 *   @OA\Property(property="tags[]", description="prompt tags", type="array", @OA\Items(type="string")),
 *   @OA\Property(property="images[]", description="prompt example images", type="array", @OA\Items(type="file")),
 *   @OA\Property(property="output_example", description="prompt output example", type="string", @OA\Examples(example="output_example", value="output_example", summary="output_example"), ), )
 * )
 */
class StorePromptRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'type'          => [
                'required',
                'exists:' . PromptType::class . ',code',
            ],
            'category'      => [
                'required',
                'exists:' . PromptCategory::class . ',id',
            ],
            'title'         => [
                'required',
                'string',
            ],
            'description'   => [
                'required',
                'string',
            ],
            'price'         => [
                'required',
                'regex:/^\d{1,3}+(\.\d{1,2})?$/',
                'min:0',
            ],
            'tags'          => [
                'nullable',
                'array',
                'between:4,7',
            ],
            'output_result' => [
                'nullable',
                'required_unless:type,image',
                'string',
            ],
            'images'        => [
                'required_if:type,image',
                'array',
                'between:1,5',
            ],
            'images.*'      => [
                'required_if:type,image',
                'image',
                'max:4096',
            ],
        ];
    }

}
