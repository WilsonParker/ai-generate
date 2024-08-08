<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseCodeResources;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="name",
 *     type="string",
 *     description="name",
 *     example="English"
 *  ),
 * )
 * Class PromptGenerateLanguageResources
 *
 * @package App\Resources\Prompt
 */
class PromptGenerateLanguageResources extends BaseCodeResources
{
    protected bool $showDateFields = false;

    public function fields($request): array
    {
        return [
            'name' => $this->name
        ];
    }

}
