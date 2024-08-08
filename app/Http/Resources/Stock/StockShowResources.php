<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\BaseIdResources;
use AIGenerate\Models\Stock\Enums\Aged;
use AIGenerate\Models\Stock\Enums\Friendly;
use AIGenerate\Models\Stock\Enums\ReviewTypes;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="ratio",
 *     type="object",
 *     description="ratio",
 *     ref="#/components/schemas/StockRatioResources"
 *  ),
 *  @OA\Property(
 *     property="isLike",
 *     type="boolean",
 *     description="isLike",
 *     example="true",
 *  ),
 *  @OA\Property(
 *     property="isFavorite",
 *     type="boolean",
 *     description="isFavorite",
 *     example="true",
 *  ),
 *  @OA\Property(
 *     property="title",
 *     type="string",
 *     description="title",
 *     example="Beautiful Asian woman holding smartphone mockup of blank screen and shows ok sign on grey background"
 *  ),
 *  @OA\Property(
 *     property="description",
 *     type="string",
 *     description="description",
 *     example="A beautiful Asian woman is holding a smartphone with a blank screen and showing an ok sign on a grey background."
 *  ),
 *  @OA\Property(
 *     property="seo_keyword",
 *     type="string",
 *     description="seo keyword",
 *     example="caucasian-beautiful-woman-smiling-home-headphones"
 *  ),
 *  @OA\Property(
 *     property="status",
 *     type="string",
 *     description="status",
 *     example="enabled"
 *  ),
 *  @OA\Property(
 *      property="image",
 *      type="string",
 *      description="image",
 *   ),
 *  @OA\Property(
 *     property="information",
 *     type="object",
 *     description="information",
 *     ref="#/components/schemas/StockInformationResources"
 *  ),
 *  @OA\Property(
 *     property="recommend",
 *     type="object",
 *     description="recommend",
 *     ref="#/components/schemas/StockRecommendResources"
 *  ),
 *  @OA\Property(
 *     property="count",
 *     type="object",
 *     description="count",
 *     ref="#/components/schemas/StockCountResources"
 *  ),
 *  @OA\Property(
 *     property="keywords",
 *     type="array",
 *     description="keywords",
 *     @OA\Items(
 *      type="string",
 *      example="phone",
 *     ),
 *    ),
 *  @OA\Property(
 *     property="generate_list",
 *     type="object",
 *     description="generate_list",
 *     ref="#/components/schemas/StockGenerateResources"
 *  ),
 *  @OA\Property(
 *     property="review_list",
 *     type="object",
 *     description="review_list",
 *     ref="#/components/schemas/StockReviewResources"
 *  ),
 * )
 * Class StockShowResources
 *
 * @package App\Resources\Stock
 */
class StockShowResources extends BaseIdResources
{
    public function fields($request): array
    {
        return [
            'ratio'         => new StockRatioResources($this),
            'isLike'        => $this->isLike,
            'isFavorite'    => $this->isFavorite,
            'title'         => $this->title,
            'description'   => $this->description,
            'seo_keyword'   => $this->keyword,
            'status'        => $this->stock_status_code,
            'image'         => $this->detailImage?->getUrl(),
            'information'   => new StockInformationResources($this->information),
            'recommend'     => new StockRecommendResources($this->recommend),
            'count'         => new StockCountResources($this->count),
            'keywords'      => $this->keywords->map(fn($item) => $item->code),
            'generate_list' => isset($this->generateList) ? $this->generateList->setCollection(
                $this->generateList->transform(fn($item) => new StockGenerateResources($item)),
            ) : null,
            'review_list'   => $this->reviewList->isNotEmpty() ?
                $this->reviewList->filter(fn($item) => $item->type !== ReviewTypes::from('feedback')->value)
                                 ->map(fn($item) => new StockReviewResources($item))->values() : null,
        ];
    }
}
