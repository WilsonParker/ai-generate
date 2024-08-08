<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseIdResources;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="prompt",
 *     type="object",
 *     description="prompt",
 *     ref="#/components/schemas/PromptResources",
 *  ),
 * )
 * Class PromptFavoriteResources
 *
 * @package App\Resources\Prompt
 */
class PromptFavoriteResources extends BaseIdResources
{
    public function fields($request): array
    {
        return [
            'prompt' => new PromptResources($this->prompt),
        ];
    }

}
