<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="UploadAvatarUserRequest",
 *   required={"avatar"},
 *   @OA\Property(property="avatar", description="avatar", type="file")
 * )
 */
class UploadAvatarUserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'avatar' => [
                'nullable',
                'image',
                'max:1024',
            ],
        ];
    }
}
