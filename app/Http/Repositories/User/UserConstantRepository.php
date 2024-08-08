<?php

namespace App\Http\Repositories\User;

use App\Http\Repositories\BaseRepository;
use AIGenerate\Models\User\Contracts\UserContract;

class UserConstantRepository extends BaseRepository
{

    public function getFirstFreeGenerateCompleted(UserContract $user): bool
    {
        return $user->constant->free_generate_completed;
    }

    public function getFirstFreeGenerateCount(UserContract $user): int
    {
        return $user->constant->free_generate_count;
    }

    public function getFirstPaidGenerate(UserContract $user): bool
    {
        return $user->constant->first_paid_generate;
    }

    public function getPromptFirstGenerated(UserContract $user): bool
    {
        return $user->constant->my_prompt_first_generated;
    }

    public function getPrompt5TimesGenerated(UserContract $user): bool
    {
        return $user->constant->my_prompt_5times_generated;
    }

    public function getPointLessThan(UserContract $user): bool
    {
        return $user->constant->point_less_than;
    }

    public function getReachGeneratedRevenue(UserContract $user): bool
    {
        return $user->constant->reach_generated_revenue;
    }

    public function updateFreeGenerateCompleted(UserContract $user): void
    {
        $user->constant->free_generate_completed = true;
        $user->constant->save();
    }

    public function updateFirstPaidGenerate(UserContract $user): void
    {
        $user->constant->first_paid_generate = true;
        $user->constant->save();
    }

    public function updatePromptFirstGenerated(UserContract $user): void
    {
        $user->constant->my_prompt_first_generated = true;
        $user->constant->save();
    }

    public function updatePrompt5TimesGenerated(UserContract $user): void
    {
        $user->constant->my_prompt_5times_generated = true;
        $user->constant->save();
    }

    public function updateReachGeneratedRevenue(UserContract $user): void
    {
        $user->constant->reach_generated_revenue = true;
        $user->constant->save();
    }

    public function updatePointLessThan(UserContract $user, bool $val = true): void
    {
        $user->constant->point_less_than = $val;
        $user->constant->save();
    }

    public function incrementFreeGenerate(UserContract $user): void
    {
        $user->constant->increment('free_generate_count');
        $user->constant->save();
    }
}
