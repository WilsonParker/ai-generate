<?php

namespace App\Services\Prompt;

use App\Http\Repositories\Prompt\PromptFavoriteRepository;
use App\Services\Prompt\Exceptions\AlreadyAddedFavorites;
use App\Services\Prompt\Sorts\Enums\Sorts;
use Illuminate\Contracts\Pagination\Paginator;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\Prompt\PromptFavorite;
use AIGenerate\Models\User\User;

class PromptFavoriteService
{
    public function __construct(
        private readonly PromptFavoriteRepository $repository,
    ) {}

    public function index(
        User $user,
        Sorts $sorts = Sorts::Newest,
        int $page = 0,
        int $size = 10,
    ): Paginator {
        return $this->repository->index($user, null, $sorts, $page, $size);
    }


    /**
     * @throws \Throwable
     */
    public function store(Prompt $prompt, User $user): PromptFavorite
    {
        throw_if($this->repository->isExistsForModel($prompt, $user), new AlreadyAddedFavorites());
        return $this->repository->storeWithModel($prompt, $user);
    }

    public function delete(PromptFavorite $promptFavorite): bool
    {
        return $this->repository->destroyWithModel($promptFavorite);
    }

    public function deleteFromModel(User $user, Prompt $prompt): bool
    {
        return $this->repository->destroy($user, $prompt);
    }
}
