<?php

namespace App\Policies\Generate;

use App\Models\Generate\ImageToImageGenerate;
use Illuminate\Auth\Access\HandlesAuthorization;
use AIGenerate\Models\User\User;

class ImageToImageGeneratePolicy
{
    use HandlesAuthorization;

    public function destroy(User $user, ImageToImageGenerate $model): bool
    {
        return $user->getKey() === $model->user_id;
    }

    public function exportImageUrl(User $user, ImageToImageGenerate $model): bool
    {
        return $user->getKey() === $model->user_id;
    }

}
