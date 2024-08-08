<?php

namespace App\Policies\Prompt;

use AIGenerate\Models\Prompt\PromptGenerate;
use AIGenerate\Models\User\User;

class PromptGeneratePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PromptGenerate $promptGenerate): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PromptGenerate $promptGenerate): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PromptGenerate $promptGenerate): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PromptGenerate $promptGenerate): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PromptGenerate $promptGenerate): bool
    {
        //
    }
}
