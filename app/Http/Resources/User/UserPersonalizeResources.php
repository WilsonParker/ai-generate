<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseIdResources;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="type",
 *     type="string",
 *     description="type",
 *     example="seller"
 *  ),
 *  @OA\Property(
 *     property="pronoun",
 *     type="string",
 *     description="pronoun",
 *     example="he"
 *  ),
 *  @OA\Property(
 *     property="interest",
 *     type="array",
 *     description="interest",
 *       @OA\Items(
 *             type="string",
 *             example="App_Design"
 *       ),
 *       example={"App_Design", "Celebrities", "Food_Recipe", "Fiction_Genres"}
 *    ),
 *  @OA\Property(
 *     property="birth",
 *     type="date",
 *     description="생년월일",
 *     example="1990-12-25"
 *  ),
 * )
 * Class UserPersonalizeResources
 *
 * @package App\Resources\User
 */
class UserPersonalizeResources extends BaseIdResources
{
    protected bool $showDateFields = false;
    protected bool $showAppendFields = false;

    public function fields(Request $request): array
    {
        return [
            'type' => $this->type,
            'pronoun' => $this->pronoun,
            'interest' => $this->interest,
            'birth' => $this->birth,
        ];
    }
}
