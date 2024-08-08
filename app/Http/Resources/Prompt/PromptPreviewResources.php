<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseIdResources;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="guide",
 *     type="string",
 *     description="guide",
 *     example="guide for prompt",
 *  ),
 *  @OA\Property(
 *     property="order",
 *     type="string",
 *     description="last order",
 *     example="order example",
 *  ),
 *  @OA\Property(
 *     property="options",
 *     type="array",
 *     description="options",
 *     @OA\Items(
 *     type="string",
 *     example="option",
 *     ),
 *  ),
 * )
 * Class PromptPreviewResources
 *
 * @package App\Resources\Prompt
 */
class PromptPreviewResources extends BaseIdResources
{
    public function toArray($request): array
    {
        return $this->resource;
    }

    function fields(Request $request): array
    {
        return [];
    }
}
