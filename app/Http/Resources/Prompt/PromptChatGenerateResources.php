<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseIdResources;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="items",
 *     type="array",
 *     description="result array",
 *         @OA\Items(ref="#/components/schemas/PromptChatGenerateResultResources"),
 *  ),
 * )
 * Class PromptChatGenerateResources
 *
 * @package App\Resources\Prompt
 */
class PromptChatGenerateResources extends BaseIdResources
{
    public function fields($request): array
    {
        return [
            'items' => $this->results->map(fn($item) => new PromptChatGenerateResultResources($item)),
        ];
    }

}
