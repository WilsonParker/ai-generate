<?php

namespace App\Observers\User;

use AIGenerate\Models\User\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param \AIGenerate\Models\User\User $user
     * @return void
     */
    public function created(User $user): void
    {
        $user->count()->create();
        $user->constant()->create();
    }
}
