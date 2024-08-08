<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseIdResources;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="name",
 *     type="string",
 *     description="name",
 *     example="WEATHER",
 *  ),
 *  @OA\Property(
 *     property="value",
 *     type="string",
 *     description="value",
 *     example="null",
 *  ),
 * )
 * Class PromptOptionResources
 *
 * @package App\Resources\Prompt
 */
class PromptOptionResources extends BaseIdResources
{
    public function fields($request): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
        ];
    }

}
