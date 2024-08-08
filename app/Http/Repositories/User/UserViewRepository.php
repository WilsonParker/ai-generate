<?php

namespace App\Http\Repositories\User;

use App\Http\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use AIGenerate\Models\User\User;

class UserViewRepository extends BaseRepository
{
    public function add(User $from, User $to): Model
    {
        $now = now()->format('Y-m-d');
        $userView = $this->firstOrCreate([
            'from_id' => $from->getKey(),
            'to_id'   => $to->getKey(),
            'date'    => $now,
        ]);
        $userView->increment('views');
        $userView->save();
        return $userView;
    }
}
