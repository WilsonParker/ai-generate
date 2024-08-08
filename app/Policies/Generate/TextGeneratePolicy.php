<?php

namespace App\Policies\Generate;

use App\Models\Generate\TextGenerate;
use Illuminate\Auth\Access\HandlesAuthorization;
use AIGenerate\Models\User\User;

class TextGeneratePolicy
{
    use HandlesAuthorization;

    public function destroy(User $user, TextGenerate $model): bool
    {
        return $user->getKey() === $model->user_id;
    }

}
