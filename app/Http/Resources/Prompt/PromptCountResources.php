<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseIdResources;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="views",
 *     type="integer",
 *     description="views",
 *     example="1"
 *  ),
 *  @OA\Property(
 *     property="favorites",
 *     type="integer",
 *     description="favorites",
 *     example="1"
 *  ),
 *  @OA\Property(
 *     property="generated",
 *     type="integer",
 *     description="generated",
 *     example="1"
 *  ),
 * )
 * Class PromptCountResources
 *
 * @package App\Resources\Prompt
 */
class PromptCountResources extends BaseIdResources
{

    public function fields($request): array
    {
        return [
            'views' => $this->views,
            'favorites' => $this->favorites,
            'generated' => $this->generated,
        ];
    }

}
