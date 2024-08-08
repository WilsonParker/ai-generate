<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\BaseIdResources;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="stock_id",
 *     type="interger",
 *     description="stock_id",
 *     example="1"
 *  ),
 *  @OA\Property(
 *     property="user_id",
 *     type="interger",
 *     description="user_id",
 *     example="1"
 *  ),
 * )
 * Class StockFavoriteResources
 *
 * @package App\Resources\Stock
 */
class StockFavoriteResources extends BaseIdResources
{
    public function fields($request): array
    {
        return [
            'stock_id' => $this->stock_id,
            'user_id' => $this->user_id,
        ];
    }
}