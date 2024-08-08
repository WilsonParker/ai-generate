<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\BaseStockResources;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="total",
 *     type="integer",
 *     description="Determine the total number of matching items in the data store",
 *     example="1000"
 *  ),
 *  @OA\Property(
 *     property="per_page",
 *     type="integer",
 *     description="The number of items to be shown per page.",
 *     example="88"
 *  ),
 *  @OA\Property(
 *     property="count",
 *     type="integer",
 *     description="Get the number of items for the current page.",
 *     example="50"
 *  ),
 *  @OA\Property(
 *     property="current_page",
 *     type="integer",
 *     description="Get the current page number.",
 *     example="2"
 *  ),
 *  @OA\Property(
 *     property="previous_page_url",
 *     type="string",
 *     description="Get the URL for the previous page.",
 *     example="http://127.0.0.1"
 *  ),
 *  @OA\Property(
 *     property="next_page_url",
 *     type="string",
 *     description="Get the URL for the next page.",
 *     example="http://127.0.0.1"
 *  ),
 * )
 * Class StockPaginateResources
 *
 * @package App\Resources\Stock
 */
class StockPaginateResources extends BaseStockResources
{
    public function fields($request): array
    {
        return [
            'total' => $this->total(),
            'per_page' => $this->perPage(),
            'count' => $this->count(),
            'current_page' => $this->currentPage(),
            'previous_page_url' => $this->previousPageUrl(),
            'next_page_url' => $this->nextPageUrl(),
        ];
    }
}
