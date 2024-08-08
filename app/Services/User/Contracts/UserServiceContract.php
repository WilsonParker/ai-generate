<?php

namespace App\Services\User\Contracts;

use App\Services\ServiceContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use AIGenerate\Models\User\User;

interface UserServiceContract extends ServiceContract
{
    public function currentUser(): Authenticatable;

    public function currentUpdate(array $attributes): User;

    public function currentUpdateAvatar(UploadedFile $avatar): User;

    public function getPrompts(User $user): int;
}
