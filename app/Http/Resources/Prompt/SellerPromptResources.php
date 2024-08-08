<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseIdResources;
use App\Services\Image\Traits\HasDefaultThumbnailTrait;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="type",
 *     type="string",
 *     description="type",
 *     example="DALL-E"
 *  ),
 *  @OA\Property(
 *     property="category",
 *     type="array",
 *     description="category",
 *     @OA\Items(
 *      type="string",
 *      @OA\Examples(example="Animal", value="Animal"),
 *     ),
 *  ),
 *  @OA\Property(
 *     property="user_id",
 *     type="integer",
 *     description="user id",
 *     example="1"
 *  ),
 *  @OA\Property(
 *     property="title",
 *     type="string",
 *     description="title",
 *     example="A cute baby sea otter"
 *  ),
 *  @OA\Property(
 *     property="thumbnail",
 *     type="string",
 *     description="thumbnail",
 *    ),
 *  @OA\Property(
 *     property="count",
 *     type="int",
 *     description="count",
 *     example="5",
 *    ),
 *  @OA\Property(
 *     property="revenue",
 *     type="string",
 *     description="revenue",
 *     example="5.5005999",
 *    ),
 *  @OA\Property(
 *     property="revenue_per_price",
 *     type="string",
 *     description="revenue_per_price",
 *     example="5.5005999",
 *    ),
 * )
 * Class SellerPromptResources
 *
 * @package App\Resources\Prompt
 */
class SellerPromptResources extends BaseIdResources
{
    use HasDefaultThumbnailTrait;

    public function fields($request): array
    {
        return [
            'type'              => $this->prompt_type_code,
            'category'          => $this->categories->pluck('name')->toArray(),
            'title'             => $this->title,
            'thumbnail'         => $this->getThumbnail($this->thumbnail),
            'count'             => $this->generates->count(),
            'revenue'           => $this->revenue,
            'revenue_per_price' => $this->revenue_per_price,
        ];
    }

}
