<?php

namespace App\Services\User;

use App\Http\Repositories\User\UserConstantRepository;
use App\Services\Prompt\PromptGenerateService;
use AIGenerate\Models\User\Contracts\UserContract;

class UserConstantService
{
    public function __construct(
        protected readonly UserConstantRepository $repository,
        protected readonly PromptGenerateService $service,
    ) {}

    public function isFirstPaidGenerate(UserContract $user): bool
    {
        return $this->isFreeGenerateCompleted($user) && !$this->repository->getFirstPaidGenerate($user);
    }

    public function isFreeGenerateCompleted(UserContract $user): bool
    {
        return $this->repository->getFirstFreeGenerateCompleted($user) || $this->repository->getFirstFreeGenerateCount($user) >= 3;
    }

    public function isPromptFirstGenerated(UserContract $user): bool
    {
        return $user->count->generated > 0 && !$this->repository->getPromptFirstGenerated($user);
    }

    public function isPrompt5TimesGenerated(UserContract $user): bool
    {
        return $user->count->generated >= 5 && !$this->repository->getPrompt5TimesGenerated($user);
    }

    public function isPointLessThan(UserContract $user): bool
    {
        return $user->getPoint() <= 2 && !$this->repository->getPointLessThan($user);
    }

    public function isReachGeneratedRevenue(UserContract $user): bool
    {
        return $this->service->getTotalSalesPrice($user) >= 10 && !$this->repository->getReachGeneratedRevenue($user);
    }

    public function updateFreeGenerateCompleted(UserContract $user): void
    {
        $this->repository->updateFreeGenerateCompleted($user);
    }

    public function updateFirstPaidGenerate(UserContract $user): void
    {
        $this->repository->updateFirstPaidGenerate($user);
    }

    public function updatePromptFirstGenerated(UserContract $user): void
    {
        $this->repository->updatePromptFirstGenerated($user);
    }

    public function updatePrompt5TimesGenerated(UserContract $user): void
    {
        $this->repository->updatePrompt5TimesGenerated($user);
    }

    public function updateReachGeneratedRevenue(UserContract $user): void
    {
        $this->repository->updateReachGeneratedRevenue($user);
    }

    public function updatePointLessThan(UserContract $user, bool $val = true): void
    {
        $this->repository->updatePointLessThan($user);
    }

    public function incrementFreeGenerate(UserContract $user): void
    {
        $this->repository->incrementFreeGenerate($user);
    }
}
