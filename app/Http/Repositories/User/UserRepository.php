<?php

namespace App\Http\Repositories\User;

use App\Http\Repositories\BaseRepository;
use App\Models\User\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserRepository extends BaseRepository
{

    public function findUserFromGoogle(string $id): Model|Authenticatable|null
    {
        return User::whereHas('information', function ($query) use ($id) {
            $query->where('google_id', $id);
        })->first();
    }

    public function createWithInformation(array $attributes): Model|Authenticatable
    {
        $user = $this->create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
        ]);
        $user->information()->create([
            'google_id' => $attributes['google_id'],
            'locale' => $attributes['locale'],
            'brevo_uuid' => Str::uuid(),
        ]);
        return $user;
    }

    public function updateInformation($id, array $attributes): Model
    {
        $user = $this->showOrFail($id);
        if (isset($attributes['name'])) {
            $user->name = $attributes['name'];
        }
        if (isset($attributes['introduction'])) {
            $user->information->introduction = $attributes['introduction'];
        }
        $user->save();
        $user->information->save();
        return $user;
    }
}
