<?php

namespace App\Http\Repositories\Prompt;

use App\Http\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\User\User;

class PromptViewRepository extends BaseRepository
{
    public function add(Prompt $prompt, User $user): Model
    {
        $now = now()->format('Y-m-d');
        $promptView = $this->firstOrCreate([
            'prompt_id' => $prompt->getKey(),
            'user_id'   => $user->getKey(),
            'date'      => $now,
        ]);
        $promptView->increment('views');
        $promptView->save();
        $prompt->save();
        return $promptView;
    }
}
