<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\BaseIdResources;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="image",
 *     type="string",
 *     description="stock image url",
 *  ),
 *  @OA\Property(
 *     property="stock",
 *     type="object",
 *     description="stock",
 *     @OA\Property(
 *        property="id",
 *        type="integer",
 *        description="stock id",
 *        example="1"
 *     ),
 *     @OA\Property(
 *        property="title",
 *        type="string",
 *        description="stock title",
 *        example="Beautiful Asian woman holding smartphone mockup of blank screen and shows ok sign on grey background"
 *     ),
 *     @OA\Property(
 *        property="seo_keyword",
 *        type="string",
 *        description="seo keyword",
 *        example="caucasian-beautiful-woman-smiling-home-headphones"
 *     ),
 *     @OA\Property(
 *        property="description",
 *        type="string",
 *        description="stock description",
 *        example="A beautiful Asian woman is holding a smartphone with a blank screen and showing an ok sign on a grey background."
 *     ),
 *  ),
 *  @OA\Property(
 *     property="thumbnail",
 *     type="string",
 *     description="stock thumbnail url",
 *  ),
 *  @OA\Property(
 *     property="aged",
 *     type="string",
 *     description="stock generate aged",
 *     example="100"
 *  ),
 *  @OA\Property(
 *     property="friendly",
 *     type="string",
 *     description="stock generate friendly",
 *     example="100"
 *  ),
 * )
 * Class StockGenerateResources
 *
 * @package App\Resources\Stock
 */
class StockGenerateResources extends BaseIdResources
{
    public function fields(Request $request): array
    {
        $image = $this->images->first();
        return [
            'image'     => $image->getUrl(),
            'stock'     => [
                'id'          => $this->stock->getKey(),
                'title'       => $this->stock->title,
                'description' => $this->stock->description,
                'seo_keyword' => $this->stock->keyword,
            ],
            'thumbnail' => $image->getUrl('gallery-thumbnail'),
            'aged'      => $image->getCustomProperty('aged'),
            'friendly'  => $image->getCustomProperty('friendly'),
        ];
    }
}
