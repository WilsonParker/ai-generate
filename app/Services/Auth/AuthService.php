<?php

namespace App\Services\Auth;

use App\Http\Repositories\User\UserRepository;
use App\Models\User\User;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function __construct(
        private readonly UserRepository $repository,
    ) {}

    public function currentUser(): Authenticatable
    {
        return Auth::guard('api')->user();
    }

    public function check(): bool
    {
        return Auth::guard('api')->check();
    }

    public function testUser(): Authenticatable
    {
        if (config('app.debug')) {
            return $this->repository->showOrFail(1);
        } else {
            throw new Exception('You can only use this function in test mode');
        }
    }

    public function pluginUser(): Authenticatable
    {
        $user = new User();
        return User::without($user->getWith())->where('email', 'plugin@gmail.com')->firstOrFail();
    }
}
