<?php

namespace App\Http\Repositories\Prompt\Contracts;

use App\Models\Prompt\Prompt;
use Illuminate\Support\Collection;
use AIGenerate\Models\User\User;

interface PromptDetailRepositoryContract extends PromptRepositoryContract
{

    public function generatedPrompts(User $user, Prompt $prompt): Collection;

    public function isFavorite(User $user, Prompt $prompt): bool;

    public function otherPrompts(Prompt $prompt): Collection;

    public function newPrompts(): Collection;

    public function popularPrompts(): Collection;

}
