<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseIdResources;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="free_generate_completed",
 *     type="bool",
 *     description="user free generate completed",
 *     example="true"
 *  ),
 *  @OA\Property(
 *     property="free_generate_count",
 *     type="int",
 *     description="user free generate count",
 *     example="1"
 *  ),
 *  @OA\Property(
 *     property="free_generate_limit",
 *     type="int",
 *     description="free_generate_limit",
 *     example="3"
 *  ),
 * )
 * Class UserInformationResources
 *
 * @package App\Resources\User
 */
class UserConstantResources extends BaseIdResources
{
    protected bool $showDateFields = false;
    protected bool $showAppendFields = false;

    public function fields(Request $request): array
    {
        return [
            'free_generate_completed' => $this->free_generate_completed,
            'free_generate_count' => $this->free_generate_count,
            'free_generate_limit' => config('constant.prompt.generate.free_limit'),
        ];
    }
}
