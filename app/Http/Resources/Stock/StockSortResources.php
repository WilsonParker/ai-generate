<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\BaseCodeResources;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="code",
 *     type="string",
 *     description="code",
 *     example="hottest"
 *  ),
 *  @OA\Property(
 *     property="name",
 *     type="string",
 *     description="name",
 *     example="Hottest"
 *  ),
 * )
 * Class StockSortResources
 *
 * @package App\Resources\Stock
 */
class StockSortResources extends BaseCodeResources
{
    protected bool $showAppendFields = false;
    protected bool $showDateFields = false;

    function fields(Request $request): array
    {
        return [
            'code' => $this->value,
            'name' => $this->name,
        ];
    }
}