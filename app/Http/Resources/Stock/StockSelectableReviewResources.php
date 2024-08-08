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
 *     example="different_picture"
 *  ),
 *  @OA\Property(
 *     property="name",
 *     type="string",
 *     description="name",
 *     example="Different from the picture"
 *  ),
 * )
 * Class StockSelectableReviewResources
 *
 * @package App\Resources\Stock
 */
class StockSelectableReviewResources extends BaseCodeResources
{
    protected bool $showAppendFields = false;
    protected bool $showDateFields = false;

    function fields(Request $request): array
    {
        return [
            'code' => $this->value,
            'name' => $this->getName(),
        ];
    }
}