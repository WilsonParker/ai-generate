<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseIdResources;
use App\Services\Image\Contracts\HasDefaultThumbnail;
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
 *     property="description",
 *     type="string",
 *     description="description",
 *     example="A cute baby sea otter"
 *  ),
 *  @OA\Property(
 *     property="guide",
 *     type="string",
 *     description="guide",
 *     example="guide example"
 *  ),
 *  @OA\Property(
 *     property="price",
 *     type="string",
 *     description="price per generate",
 *     example="1"
 *  ),
 *  @OA\Property(
 *     property="tags",
 *     type="array",
 *     description="tags",
 *     @OA\Items(
 *      type="string",
 *      @OA\Examples(example="cute", value="cute"),
 *      @OA\Examples(example="sea otter", value="sea otter"),
 *     ),
 *    ),
 *  @OA\Property(
 *     property="images",
 *     type="array",
 *     description="images",
 *     @OA\Items(
 *      type="string",
 *     ),
 *    ),
 *  @OA\Property(
 *     property="options",
 *     type="array",
 *     description="options",
 *     @OA\Items(ref="#/components/schemas/PromptOptionResources"),
 *  ),
 * )
 * Class PromptResources
 *
 * @package App\Resources\Prompt
 */
class PromptResources extends BaseIdResources implements HasDefaultThumbnail
{
    use HasDefaultThumbnailTrait;

    public function fields($request): array
    {
        return [
            'type'          => $this->prompt_type_code,
            'category'      => $this->categories->pluck('name')->toArray(),
            'status'        => $this->prompt_status_code,
            'user_id'       => $this->user_id,
            'title'         => $this->title,
            'guide'         => $this->guide,
            'output_result' => $this->output_result,
            'order'         => $this->order,
            'description'   => $this->description,
            'price'         => $this->price_per_generate,
            'tags'          => $this->tags->pluck('name')->toArray(),
            'options'       => $this->options->map(fn($item) => new PromptOptionResources($item)),
            'images'        => $this->images->map(fn($item) => $this->getThumbnail($item)),
            'thumbnail'     => $this->getThumbnail($this->thumbnail),
        ];
    }

}
