<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\BaseIdResources;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="type",
 *     type="string",
 *     description="type",
 *     example="amazing"
 *  ),
 *  @OA\Property(
 *     property="memo",
 *     type="string",
 *     description="memo",
 *     example="Blah blah blah blah"
 *  ),
 * )
 * Class StockReviewResources
 *
 * @package App\Resources\Stock
 */
class StockReviewResources extends BaseIdResources
{
    protected bool $showDateFields = false;
    protected bool $showAppendFields = false;

    public function fields($request): array
    {
        return [
            'type' => $this->type,
            'memo' => $this->memo,
        ];
    }
}