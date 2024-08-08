<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\BaseIdResources;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="title",
 *     type="string",
 *     description="stock title",
 *     example="Beautiful Asian woman holding smartphone mockup of blank screen and shows ok sign on grey background"
 *  ),
 *  @OA\Property(
 *     property="seo_keyword",
 *     type="string",
 *     description="seo keyword",
 *     example="caucasian-beautiful-woman-smiling-home-headphones"
 *  ),
 *  @OA\Property(
 *     property="description",
 *     type="string",
 *     description="stock description",
 *     example="A beautiful Asian woman is holding a smartphone with a blank screen and showing an ok sign on a grey background."
 *  ),
 *  @OA\Property(
 *     property="status",
 *     type="string",
 *     description="stock status",
 *     example="enabled"
 *  ),
 *  @OA\Property(
 *     property="keyword",
 *     type="string",
 *     description="stock keyword",
 *     example="beautiful-asian-woman-holding-smartphone-mockup"
 *  ),
 *  @OA\Property(
 *     property="thumbnail",
 *     type="string",
 *     description="stock thumbnail url",
 *  ),
 * )
 * Class StockListResources
 *
 * @package App\Resources\Stock
 */
class StockListResources extends BaseIdResources
{
    public function fields(Request $request): array
    {
        return [
            'title'       => $this->title,
            'seo_keyword' => $this->keyword,
            'description' => $this->description,
            'status'      => $this->stock_status_code,
            'thumbnail'   => $this->detailImage?->getUrl('detail-thumbnail') ?? config('constant.images.default'),
        ];
    }
}
