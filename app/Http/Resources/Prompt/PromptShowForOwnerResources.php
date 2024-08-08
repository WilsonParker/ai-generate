<?php

namespace App\Http\Resources\Prompt;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="prompt",
 *     type="string",
 *     description="prompt",
 *     example="prompt example"
 *  ),
 *  @OA\Property(
 *     property="engine",
 *     type="string",
 *     description="engine",
 *     example="gpt-3.5-turbo"
 *  ),
 * )
 * Class PromptShowForOwnerResources
 *
 * @package App\Resources\Prompt
 */
class PromptShowForOwnerResources extends PromptResources
{
    public function fields($request): array
    {
        return array_merge(
            parent::fields($request),
            [
                'prompt' => $this->prompt,
                'engine' => $this->prompt_engine_code,
            ],
        );
    }


}
