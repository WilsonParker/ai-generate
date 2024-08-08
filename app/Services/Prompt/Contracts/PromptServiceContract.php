<?php

namespace App\Services\Prompt\Contracts;

use App\Services\Prompt\Sorts\Enums\Sorts;
use App\Services\ServiceContract;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\User\User;

interface PromptServiceContract extends ServiceContract
{
    public function index(
        string $search = null,
        Sorts $sorts = Sorts::Newest,
        int $page = 0,
        int $size = 10,
        int $last = 0,
        array $types = [],
        array $categories = [],
    ): Paginator|Collection;

    public function main(): Collection;

    public function store(array $attributes): Prompt;

    public function delete(Prompt $prompt): bool;

    public function getCategories(): Collection;

    public function getTypes(): Collection;

    public function getEngines(Prompt $prompt): Collection;

    public function storeWithOptions(
        User $user,
        string $title,
        string $description,
        string $template,
        string $promptTypeCode,
    ): Prompt;

    public function generatedPrompts(User $user, Prompt $prompt): Collection;

    public function getLanguages(): \Illuminate\Database\Eloquent\Collection;

    public function getTones(): Collection;

    public function getWritingStyles(): Collection;

    public function otherPrompts(Prompt $prompt): Collection;

    public function newPrompts(): Collection;

    public function popularPrompts(): Collection;

    public function isFavorite(User $user, Prompt $prompt): bool;
}
