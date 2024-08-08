<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseCodeResources;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="name",
 *     type="string",
 *     description="name",
 *     example="DALL-E"
 *  ),
 *  @OA\Property(
 *     property="type",
 *     type="string",
 *     description="type",
 *     example="image"
 *  ),
 * )
 * Class PromptTypeResources
 *
 * @package App\Resources\Prompt
 */
class PromptTypeResources extends BaseCodeResources
{
    protected bool $showDateFields = false;

    function fields(Request $request): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
        ];
    }
}
