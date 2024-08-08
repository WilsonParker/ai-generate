<?php

namespace App\Services\User;

use App\Http\Repositories\User\UserRepository;
use App\Services\Auth\Facades\AuthService;
use App\Services\Image\Contracts\ImageServiceContract;
use App\Services\User\Contracts\UserServiceContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use AIGenerate\Models\User\User;

class UserService implements UserServiceContract
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly ImageServiceContract $imageService,
    ) {}

    public function createName(): string
    {
        return Str::random(16);
    }

    public function currentUser(): Authenticatable
    {
        return AuthService::currentUser();
    }

    public function currentUpdate(array $attributes): User
    {
        return $this->repository->updateInformation($this->currentUser()->getKey(), $attributes);
    }

    public function currentUpdateAvatar(UploadedFile $avatar): User
    {
        $user = $this->currentUser();
        $image = $this->imageService->upload($user->information, $avatar, 'avatar');
        $user->information->avatar = $this->imageService->getLink($image);
        $user->information->save();
        return $user;
    }


    public function getPrompts(User $user): int
    {
        return $user->prompts()->count();
    }
}
