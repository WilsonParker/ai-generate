<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\BaseIdResources;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="ethnicity",
 *     type="string",
 *     description="ethnicity",
 *     example="african american"
 *  ),
 *  @OA\Property(
 *     property="gender",
 *     type="string",
 *     description="gender",
 *     example="male"
 *  ),
 *  @OA\Property(
 *     property="people_number",
 *     type="integer",
 *     description="number of people",
 *     example="1"
 *  ),
 * )
 * Class StockInformationResources
 *
 * @package App\Resources\Stock
 */
class StockInformationResources extends BaseIdResources
{
    protected bool $showAppendFields = false;
    protected bool $showDateFields = false;

    public function fields($request): array
    {
        return [
            'ethnicity' => $this->ethnicity,
            'gender' => $this->gender,
            'people_number' => $this->people_number,
        ];
    }
}