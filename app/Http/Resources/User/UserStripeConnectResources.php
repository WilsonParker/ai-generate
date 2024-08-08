<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseIdResources;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="connect_id",
 *     type="string",
 *     description="stripe connect account id",
 *     example="acct_1N2YaUQX6LUUAzv7"
 *  ),
 *  @OA\Property(
 *     property="express_status",
 *     type="string",
 *     description="express status",
 *     example="inactive"
 *  ),
 *  @OA\Property(
 *     property="transfer_status",
 *     type="string",
 *     description="transfer status",
 *     example="inactive"
 *  ),
 * )
 * Class UserStripeConnectResources
 *
 * @package App\Resources\User
 */
class UserStripeConnectResources extends BaseIdResources
{
    protected bool $showDateFields = false;
    protected bool $showAppendFields = false;

    public function fields(Request $request): array
    {
        return [
            'connect_id' => $this->connect_id,
            'express_status' => $this->express_status,
            'transfer_status' => $this->transfer_status,
        ];
    }
}
