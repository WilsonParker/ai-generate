<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseIdResources;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="views",
 *     type="integer",
 *     description="view count",
 *     example="100"
 *  ),
 *  @OA\Property(
 *     property="follows",
 *     type="integer",
 *     description="follow count",
 *     example="100"
 *  ),
 *  @OA\Property(
 *     property="followings",
 *     type="integer",
 *     description="following count",
 *     example="100"
 *  ),
 *  @OA\Property(
 *     property="generates",
 *     type="integer",
 *     description="generates count",
 *     example="100"
 *  ),
 *  @OA\Property(
 *     property="generated",
 *     type="integer",
 *     description="generated count",
 *     example="100"
 *  ),
 *  @OA\Property(
 *     property="stock_generates",
 *     type="integer",
 *     description="stock generates count",
 *     example="100"
 *  ),
 * )
 * Class UserCountResources
 *
 * @package App\Resources\User
 */
class UserCountResources extends BaseIdResources
{
    protected bool $showDateFields = false;
    protected bool $showAppendFields = false;

    public function fields(Request $request): array
    {
        return [
            'views' => $this->views,
            'follows' => $this->follows,
            'followings' => $this->followings,
            'generates' => $this->generates,
            'generated' => $this->generated,
            'prompts' => $this->prompts,
            'stock_generates' => $this->stock_generates,
            'text_generates' => $this->text_generates,
        ];
    }
}
