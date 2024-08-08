<?php

namespace App\Services\Prompt\FreeGenerateComposite;

use App\Services\Auth\Facades\AuthService;
use App\Services\Prompt\FreeGenerateComposite\Contracts\CanGenerateForFree;
use App\Services\User\UserConstantService;

/*
 * 처음 Generate 하는 경우 무료
 * */

class FirstGenerateUsingConstantComposite implements CanGenerateForFree
{
    public function __construct(
        private readonly UserConstantService $service
    ) {}

    function isFree(): bool
    {
        return !$this->service->isFreeGenerateCompleted(AuthService::currentUser());
    }

}
