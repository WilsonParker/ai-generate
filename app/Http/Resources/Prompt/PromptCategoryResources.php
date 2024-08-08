<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseIdResources;
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
 * )
 * Class PromptCategoryResources
 *
 * @package App\Resources\Prompt
 */
class PromptCategoryResources extends BaseIdResources
{
    protected bool $showDateFields = false;

    function fields(Request $request): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
