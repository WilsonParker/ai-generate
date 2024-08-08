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
 *         @OA\Items(ref="#/components/schemas/PromptImageGenerateResultResources"),
 *  ),
 *  @OA\Property(
 *     property="expired_at",
 *     type="datetime",
 *     description="expired date",
 *     example="2023-01-01 00:00:00"
 *  )
 * )
 * Class PromptImageGenerateResources
 *
 * @package App\Resources\Prompt
 */
class PromptImageGenerateResources extends BaseIdResources
{
    public function fields($request): array
    {
        return [
            'items' => $this->results->map(fn($item) => new PromptImageGenerateResultResources($item)),
            'expired_at' => $this->formatDateTime($this->expired_at),
        ];
    }

}
