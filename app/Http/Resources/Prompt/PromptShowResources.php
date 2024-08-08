<?php

namespace App\Http\Resources\Prompt;

use OpenApi\Annotations as OA;
use AIGenerate\Services\AI\OpenAI\Enums\OpenAITypes;
use AIGenerate\Services\AI\OpenAI\Images\ImageSize;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="isFavorite",
 *     type="bool",
 *     description="isFavorite",
 *     example="true",
 *  ),
 *  @OA\Property(
 *     property="generated",
 *     type="array",
 *     description="generated",
 *     @OA\Items(ref="#/components/schemas/PromptGenerateItemResources"),
 *  ),
 *  @OA\Property(
 *     property="others",
 *     type="array",
 *     description="others",
 *     @OA\Items(ref="#/components/schemas/PromptResources"),
 *  ),
 *  @OA\Property(
 *     property="new",
 *     type="array",
 *     description="new",
 *     @OA\Items(ref="#/components/schemas/PromptResources"),
 *  ),
 *  @OA\Property(
 *     property="popular",
 *     type="array",
 *     description="popular",
 *     @OA\Items(ref="#/components/schemas/PromptResources"),
 *  ),
 * )
 * Class PromptShowResources
 *
 * @package App\Resources\Prompt
 */
class PromptShowResources extends PromptResources
{
    public function fields($request): array
    {
        $ratio = [];
        if (OpenAITypes::from($this->prompt_type_code) === OpenAITypes::Image) {
            $ratio = ImageSize::cases();
        }
        return array_merge(
            parent::fields($request),
            [
                'isFavorite' => $this->isFavorite,
                'ratio'      => $ratio,
                'generated'  => $this->generated->map(fn($item) => new PromptGenerateItemResources($item))->toArray(),
                'others'     => $this->others->map(fn($item) => new PromptResources($item))->toArray(),
                'new'        => $this->new->map(fn($item) => new PromptResources($item))->toArray(),
                'popular'    => $this->popular->map(fn($item) => new PromptResources($item))->toArray(),
                'count'      => new PromptCountResources($this->count),
            ],
        );
    }


}
