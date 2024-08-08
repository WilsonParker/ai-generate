<?php

namespace App\Policies\Prompt;

use App\Models\Prompt\Prompt;
use App\Models\User\User;
use AIGenerate\Models\Prompt\Enums\Status;

class PromptPolicy
{

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Prompt $prompt): bool
    {
        return $prompt->prompt_status_code == Status::Enabled->value;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Prompt $prompt): bool
    {
        return $user->getKey() == $prompt->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Prompt $prompt): bool
    {
        return $user->getKey() == $prompt->user_id;
    }

    public function showForOwner(User $user, Prompt $prompt): bool
    {
        return $user->getKey() == $prompt->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Prompt $prompt): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Prompt $prompt): bool
    {
        return $user->getKey() == $prompt->user_id;
    }
}
