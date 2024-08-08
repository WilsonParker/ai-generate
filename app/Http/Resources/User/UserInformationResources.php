<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseIdResources;
use App\Services\Image\Contracts\HasDefaultThumbnail;
use App\Services\Image\Traits\HasDefaultThumbnailTrait;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="avatar",
 *     type="string",
 *     description="user profile image",
 *     example="https://lh3.googleusercontent.com/a/AGNmyxZp8gfkgP_EJeZARoI5qsVgHHF4oxFlPz2rIP9C=s96-c"
 *  ),
 *  @OA\Property(
 *     property="locale",
 *     type="string",
 *     description="user locale",
 *     example="ko"
 *  ),
 *  @OA\Property(
 *     property="introduce",
 *     type="string",
 *     description="user introduce",
 *     example="example introduce"
 *  ),
 *  @OA\Property(
 *     property="brevo_uuid",
 *     type="string",
 *     description="brevo uuid",
 *     example="testuuid1234"
 *  ),
 * )
 * Class UserInformationResources
 *
 * @package App\Resources\User
 */
class UserInformationResources extends BaseIdResources implements HasDefaultThumbnail
{
    protected bool $showDateFields = false;
    protected bool $showAppendFields = false;
    
    use HasDefaultThumbnailTrait;

    public function fields(Request $request): array
    {
        return [
            'avatar' => $this->getThumbnail($this->avatar, config('constant.images.avatar')),
            'introduction' => $this->introduction,
            'locale' => $this->locale,
            'brevo_uuid' => $this->brevo_uuid,
        ];
    }
}
