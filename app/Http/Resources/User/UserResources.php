<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseIdResources;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="name",
 *     type="string",
 *     description="user name",
 *     example="Sonya Barton"
 *  ),
 *  @OA\Property(
 *     property="email",
 *     type="string",
 *     description="user email",
 *     example="lehner.michele@example.org"
 *  ),
 *  @OA\Property(
 *     property="point",
 *     type="string",
 *     description="user point",
 *     example="194.487"
 *  ),
 *  @OA\Property(
 *     property="information",
 *     type="object",
 *     description="user information",
 *     ref="#/components/schemas/UserInformationResources"
 *  ),
 *  @OA\Property(
 *     property="count",
 *     type="object",
 *     description="user count information",
 *     ref="#/components/schemas/UserCountResources"
 *  ),
 *  @OA\Property(
 *     property="constant",
 *     type="object",
 *     description="user constant",
 *     ref="#/components/schemas/UserConstantResources"
 *  ),
 *  @OA\Property(
 *     property="stripe_customer",
 *     type="object",
 *     description="user stripe customer account information",
 *     ref="#/components/schemas/UserStripeCustomerResources"
 *  ),
 *  @OA\Property(
 *     property="stripe_connect",
 *     type="object",
 *     description="user stripe connect account information",
 *     ref="#/components/schemas/UserStripeConnectResources"
 *  ),
 *  @OA\Property(
 *     property="personalize",
 *     type="object",
 *     description="personalize information",
 *     ref="#/components/schemas/UserPersonalizeResources"
 *  ),
 * )
 * Class UserResources
 *
 * @package App\Resources\User
 */
class UserResources extends BaseIdResources
{
    public function fields(Request $request): array
    {

        return [
            'name' => $this->name,
            'email' => $this->email,
            'point' => $this->getPoint(),
            'information' => new UserInformationResources($this->information),
            'counts' => new UserCountResources($this->count),
            'constant' => new UserConstantResources($this->constant),
            'stripe_customer' => new UserStripeCustomerResources($this->stripeCustomerAccount),
            'stripe_connect' => new UserStripeConnectResources($this->stripeConnectAccount),
            'personalize' => new UserPersonalizeResources($this->personalize),
        ];
    }
}
