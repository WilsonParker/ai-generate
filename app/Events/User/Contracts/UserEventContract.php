<?php

namespace App\Events\User\Contracts;

use AIGenerate\Models\User\User;

interface UserEventContract
{
    public function getUser(): User;
}
