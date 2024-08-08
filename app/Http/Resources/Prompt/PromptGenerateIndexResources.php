<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseIdResources;
use OpenApi\Annotations as OA;
use AIGenerate\Services\AI\OpenAI\Enums\OpenAITypes;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="order",
 *     type="string",
 *     description="order",
 *     example="order",
 *  ),
 *  @OA\Property(
 *     property="max_tokens",
 *     type="integer",
 *     description="max_tokens",
 *     example="256",
 *  ),
 *  @OA\Property(
 *     property="image_size",
 *     type="string",
 *     description="image size",
 *     example="512x512",
 *  ),
 *  @OA\Property(
 *     property="prompt",
 *     type="object",
 *     description="prompt",
 *     ref="#/components/schemas/PromptResources",
 *  ),
 *  @OA\Property(
 *     property="results",
 *     type="array",
 *     description="results",
 *     @OA\Items(ref="#/components/schemas/PromptImageGenerateResultResources"),
 *     @OA\Items(ref="#/components/schemas/PromptChatGenerateResultResources"),
 *     @OA\Items(ref="#/components/schemas/PromptCompletionGenerateResultResources"),
 *  ),
 *  @OA\Property(property="expired_at", type="datetime", description="expired timestamp", readOnly="true", example="2023-01-01 00:00:00"),
 * )
 * Class PromptGenerateIndexResources
 *
 * @package App\Resources\Prompt
 */
class PromptGenerateIndexResources extends BaseIdResources
{
    public function fields($request): array
    {
        $type = OpenAITypes::from($this->prompt->prompt_type_code);
        return [
            'order'      => $this->order,
            'max_tokens' => $this->max_tokens,
            'image_size' => $this->image_size,
            'prompt'     => new PromptResources($this->prompt),
            'results'    => $this->results->map(fn($result) => $type->newResources($result)),
            'expired_at' => $this->formatDateTime($this->expired_at),
        ];
    }

}
