<?php

namespace App\Http\Resources\Generate;

use App\Http\Resources\BaseIdResources;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="image",
 *     type="string",
 *     description="image url",
 *  ),
 * @OA\Property(
 *     property="thumbnail",
 *     type="string",
 *     description="thumbnail url",
 *  ),
 * @OA\Property(
 *     property="aged",
 *     type="string",
 *     description="generate aged",
 *     example="100"
 *  ),
 * @OA\Property(
 *      property="prompt",
 *      type="string",
 *      description="prompt",
 *   ),
 * @OA\Property(
 *      property="type",
 *      type="string",
 *      description="type",
 *   ),
 *  @OA\Property(
 *      property="ratio",
 *      type="string",
 *      description="ratio",
 *   ),
 *   @OA\Property(
 *      property="gender",
 *      type="string",
 *      description="gender",
 *   ),
 *   @OA\Property(
 *      property="ethnicity",
 *      type="string",
 *      description="ethnicity",
 *   ),
 *   @OA\Property(
 *       property="age",
 *       type="integer",
 *       description="age",
 *    ),
 * )
 * Class TextGenerateResources
 *
 * @package App\Resources\Generate
 */
class TextGenerateResources extends BaseIdResources
{
    public function fields(Request $request): array
    {
        $image = $this->images->first();
        return [
            'image'     => $image?->getUrl(),
            'thumbnail' => $image?->getUrl('gallery-thumbnail') ?? '',
            'prompt'    => $this->prompt,
            'type'      => $this->type_code,
            'ratio'     => $this->ratio,
            'gender'    => $this->gender,
            'ethnicity' => $this->ethnicity,
            'age'       => $this->age,
        ];
    }
}
