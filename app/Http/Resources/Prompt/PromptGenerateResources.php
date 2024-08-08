<?php

namespace App\Http\Resources\Prompt;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="prompt",
 *     type="object",
 *     description="prompt",
 *     ref="#/components/schemas/PromptResources",
 *  ),
 * )
 * Class PromptGenerateResources
 *
 * @package App\Resources\Prompt
 */
class PromptGenerateResources extends BasePromptGenerateResources
{
    public function fields($request): array
    {
        return array_merge(
            parent::fields($request),
            [
                'prompt' => new PromptResources($this->prompt),
            ],
        );
    }

}
