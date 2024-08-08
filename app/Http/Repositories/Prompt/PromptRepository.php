<?php

namespace App\Http\Repositories\Prompt;

use App\Http\Repositories\BaseRepository;
use App\Http\Repositories\Prompt\Contracts\PromptDetailRepositoryContract;
use App\Models\Prompt\Prompt;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use AIGenerate\Models\User\User;

class PromptRepository extends BaseRepository implements PromptDetailRepositoryContract
{

    public function index(array $attributes, callable $sortCallback): Paginator
    {
        return Prompt::search($attributes['search'] ?? null)
                     ->when($attributes['sort'] ?? null, function ($query) use ($attributes, $sortCallback) {
                         $sortCallback($query);
                     })
                     ->paginate($attributes['size'] ?? 10, 'page', $attributes['page'] ?? 1);
    }

    public function generatedPrompts(User $user, Prompt $prompt): Collection
    {
        return $user->promptGenerates()->where('prompt_id', $prompt->getKey())->get();
    }

    public function otherPrompts(Prompt $prompt): Collection
    {
        return $this->model->where('id', '!=', $prompt->getKey())->get();
    }

    public function main(): Collection
    {
        return $this->model::inRandomOrder()->limit(10)->get();
    }

    public function newPrompts(): Collection
    {
        return $this->model::orderBy('created_at', 'desc')->limit(10)->get();
    }

    public function popularPrompts(int $limit = 10): Collection
    {
        return $this->model::orderBy('created_at', 'desc')->limit($limit)->get();
    }

    public function isFavorite(User $user, Prompt $prompt): bool
    {
        return $user->favorites()->where('prompt_id', $prompt->getKey())->exists();
    }
}
