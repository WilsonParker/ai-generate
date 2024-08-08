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
 *     example="male"
 *  ),
 *  @OA\Property(
 *     property="name",
 *     type="string",
 *     description="name",
 *     example="Male"
 *  ),
 * )
 * Class StockFilterGenderResources
 *
 * @package App\Resources\Stock
 */
class StockFilterGenderResources extends BaseCodeResources
{
    protected bool $showDateFields = false;
    protected bool $showAppendFields = false;

    function fields(Request $request): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
        ];
    }
}