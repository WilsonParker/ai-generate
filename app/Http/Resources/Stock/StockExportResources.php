<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\BaseIdResources;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="image_url",
 *     type="string",
 *     description="image url",
 *     example="https://example.com/image.png"
 *  ),
 *  @OA\Property(
 *     property="user_id",
 *     type="integer",
 *     description="user id",
 *     example="1"
 *  ),
 *  @OA\Property(
 *     property="stock_id",
 *     type="integer",
 *     description="stock id",
 *     example="1"
 *  ),
 * )
 * Class StockCountResources
 *
 * @package App\Resources\Stock\StockExportResources
 */
class StockExportResources extends BaseIdResources
{
    public function fields($request): array
    {
        return [
            'image_url' => $this->imageUrl,
            'user_id' => $this->generate->user_id,
            'stock_id' => $this->generate->stock_id,
            'exports' => $this->generate->user->count->exports,
        ];
    }
}
