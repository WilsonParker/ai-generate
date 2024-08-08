<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseIdResources;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="customer_id",
 *     type="string",
 *     description="stripe customer account id",
 *     example="cus_Non4PmuFXyOIpa"
 *  ),
 * )
 * Class UserStripeCustomerResources
 *
 * @package App\Resources\User
 */
class UserStripeCustomerResources extends BaseIdResources
{
    protected bool $showDateFields = false;
    protected bool $showAppendFields = false;

    public function fields(Request $request): array
    {
        return [
            'customer_id' => $this->customer_id,
        ];
    }
}
