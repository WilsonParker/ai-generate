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
 *         @OA\Items(ref="#/components/schemas/PromptCompletionGenerateResultResources"),
 *  ),
 * )
 * Class PromptCompletionGenerateResources
 *
 * @package App\Resources\Prompt
 */
class PromptCompletionGenerateResources extends BaseIdResources
{
    public function fields($request): array
    {
        return [
            'items' => $this->results->map(fn($item) => new PromptCompletionGenerateResultResources($item)),
        ];
    }

}
