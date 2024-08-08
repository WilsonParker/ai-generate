<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\BaseIdResources;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="generates",
 *     type="integer",
 *     description="generates",
 *     example="10"
 *  ),
 *  @OA\Property(
 *     property="views",
 *     type="integer",
 *     description="views",
 *     example="5"
 *  ),
 *  @OA\Property(
 *     property="likes",
 *     type="integer",
 *     description="likes",
 *     example="1"
 *  ),
 * )
 * Class StockCountResources
 *
 * @package App\Resources\Stock
 */
class StockCountResources extends BaseIdResources
{
    protected bool $showDateFields = false;

    public function fields($request): array
    {
        return [
            'generates' => $this->generates,
            'views' => $this->views,
            'likes' => $this->likes,
        ];
    }
}