<?php

namespace App\Policies\User;

use Illuminate\Auth\Access\HandlesAuthorization;
use AIGenerate\Models\User\User;

class UserPolicy
{
    use HandlesAuthorization;

    public function update(User $user, User $model): bool
    {
        return $user->getKey() === $model->getKey();
    }

}
