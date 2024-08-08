<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\BaseIdResources;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="rWidth",
 *     type="number",
 *     format="float",
 *     description="Width of the image",
 *     example=1000.1
 *  ),
 *  @OA\Property(
 *     property="rHeight",
 *     type="number",
 *     format="float",
 *     description="rHeight of the image",
 *     example=1000.1
 *  ),
 * )
 * Class StockRatioResources
 *
 * @package App\Resources\Stock
 */
class StockRatioResources extends BaseIdResources
{
    protected bool $showAppendFields = false;
    protected bool $showDateFields = false;

    public function fields($request): array
    {
        $image = $this->images->first();
        return [
            'rWidth' => $image->getCustomProperty('rWidth'),
            'rHeight' => $image->getCustomProperty('rHeight'),
        ];
    }
}
