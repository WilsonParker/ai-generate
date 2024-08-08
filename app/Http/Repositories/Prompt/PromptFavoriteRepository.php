<?php

namespace App\Http\Repositories\Prompt;

use App\Http\Repositories\BaseRepository;
use App\Services\Prompt\Sorts\Enums\Sorts;
use Illuminate\Contracts\Pagination\Paginator;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\Prompt\PromptFavorite;
use AIGenerate\Models\User\User;

class PromptFavoriteRepository extends BaseRepository
{
    public function index(
        User $user,
        string $search = null,
        Sorts $sorts = Sorts::Newest,
        int $page = 0,
        int $size = 10,
    ): Paginator {
        return $user->favorites()
                    ->orderBy('id', 'desc')
                    ->paginate($size, ['*'], 'page', $page);
    }

    public function storeWithModel(Prompt $prompt, User $user): PromptFavorite
    {
        return $this->create([
            'prompt_id' => $prompt->id,
            'user_id'   => $user->id,
        ]);
    }

    public function isExistsForModel(Prompt $prompt, User $user): bool
    {
        return $this->model::where('prompt_id', $prompt->id)
                           ->where('user_id', $user->id)
                           ->exists();
    }

    public function destroyWithModel(PromptFavorite $promptFavorite): ?bool
    {
        return $promptFavorite->delete();
    }

    public function destroy(User $user, Prompt $prompt): ?bool
    {
        $model = $this->model::where('prompt_id', $prompt->getKey())
                             ->where('user_id', $user->getKey())
                             ->firstOrFail();
        return $model->delete();
    }
}
