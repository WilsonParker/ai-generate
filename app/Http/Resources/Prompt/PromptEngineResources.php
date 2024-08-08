<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseCodeResources;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="code",
 *     type="string",
 *     description="name",
 *     example="gpt-3.5-turbo"
 *  ),
 * )
 * Class PromptEngineResources
 *
 * @package App\Resources\Prompt
 */
class PromptEngineResources extends BaseCodeResources
{
    function fields(Request $request): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
