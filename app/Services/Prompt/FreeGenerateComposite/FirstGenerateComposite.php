<?php

namespace App\Services\Prompt\FreeGenerateComposite;

use App\Http\Repositories\Prompt\PromptGenerateRepository;
use App\Services\Auth\Facades\AuthService;
use App\Services\Prompt\FreeGenerateComposite\Contracts\CanGenerateForFree;

/*
 * 처음 Generate 하는 경우 무료
 * */

class FirstGenerateComposite implements CanGenerateForFree
{
    public function __construct(
        private readonly PromptGenerateRepository $repository
    ) {}

    function isFree(): bool
    {
        $list = $this->repository->getGenerateList([
            'user' => AuthService::currentUser(),
        ], function () {});
        return $list->count() <= 3;
    }

}
