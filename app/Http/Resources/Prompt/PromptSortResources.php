<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseCodeResources;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="id",
 *     type="string",
 *     description="id",
 *     example="newest"
 *  ),
 *  @OA\Property(
 *     property="name",
 *     type="string",
 *     description="name",
 *     example="newest"
 *  ),
 * )
 * Class PromptSortResources
 *
 * @package App\Resources\Prompt
 */
class PromptSortResources extends BaseCodeResources
{
    protected bool $showAppendFields = false;
    protected bool $showDateFields = false;

    function fields(Request $request): array
    {
        return [
            'id' => $this->value,
            'name' => $this->name,
        ];
    }
}
