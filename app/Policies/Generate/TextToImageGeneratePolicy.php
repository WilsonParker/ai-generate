<?php

namespace App\Policies\Generate;

use App\Models\Generate\TextToImageGenerate;
use Illuminate\Auth\Access\HandlesAuthorization;
use AIGenerate\Models\User\User;

class TextToImageGeneratePolicy
{
    use HandlesAuthorization;

    public function destroy(User $user, TextToImageGenerate $model): bool
    {
        return $user->getKey() === $model->user_id;
    }

    public function exportImageUrl(User $user, TextToImageGenerate $model): bool
    {
        return $user->getKey() === $model->user_id;
    }

}
