<?php

namespace App\Observers\User;

use AIGenerate\Models\User\UserView;

class UserViewObserver
{
    public function updated(UserView $userView): void
    {
        $userView->to->count->increment('views');
    }
}
