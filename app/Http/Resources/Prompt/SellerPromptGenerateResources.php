<?php

namespace App\Http\Resources\Prompt;

use Illuminate\Support\Arr;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="prompt",
 *     type="object",
 *     description="prompt",
 *     ref="#/components/schemas/SellerPromptResources",
 *  ),
 * )
 * Class PromptGenerateResources
 *
 * @package App\Resources\Prompt
 */
class SellerPromptGenerateResources extends BasePromptGenerateResources
{
    public function fields($request): array
    {
        return array_merge(
            Arr::only(parent::fields($request), ['order', 'max_token', 'price']),
            [
                'prompt' => new SellerPromptResources($this->prompt),
            ],
        );
    }

}
