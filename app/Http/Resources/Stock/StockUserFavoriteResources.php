<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\BaseStockResources;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="paginate",
 *     type="object",
 *     description="paginate information",
 *     ref="#/components/schemas/StockPaginateResources"
 *  ),
 *  @OA\Property(
 *     property="stock",
 *     type="array",
 *     description="stock information",
 *     @OA\Items(ref="#/components/schemas/StockListResources"),
 *  ),
 * )
 * Class StockUserFavoriteResources
 *
 * @package App\Resources\Stock
 */
class StockUserFavoriteResources extends BaseStockResources
{
    public function fields($request): array
    {
        return [
            'paginate' => new StockPaginateResources($this),
            'stock' => collect($this->items())->map(fn($item) => new StockListResources($item->stock)),
        ];
    }
}