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
 *      property="prompt",
 *      type="string",
 *      description="prompt",
 *   ),
 * @OA\Property(
 *       property="is_success",
 *       type="boolean",
 *    ),
 * )
 * Class TextToImageGenerateResources
 *
 * @package App\Resources\Generate
 */
class TextToImageGenerateResources extends BaseIdResources
{
    public function fields(Request $request): array
    {
        $image = $this->images->first();
        return [
            'image'      => $image?->getUrl(),
            'thumbnail'  => $image?->getUrl('gallery-thumbnail') ?? '',
            'prompt'     => $this->prompt,
            'is_success' => $this->is_success,
        ];
    }
}
