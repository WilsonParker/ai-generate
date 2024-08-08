<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\BaseIdResources;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="stock_id",
 *     type="integer",
 *     description="stock id",
 *     example="1"
 *  ),
 * )
 * Class StockRecommendResources
 *
 * @package App\Resources\Stock
 */
class StockRecommendResources extends BaseIdResources
{
    protected bool $showDateFields = false;

    public function fields($request): array
    {
        return [
            'stock_id' => $this->stock_id,
        ];
    }
}