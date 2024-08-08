<?php

namespace App\Http\Repositories\Generate;

use App\Http\Repositories\BaseRepository;
use App\Models\Generate\TextGenerate;
use App\Models\User\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Foundation\Auth\User as Authenticatable;
use AIGenerate\Models\Generate\Enums\TextGenerateType;

class TextGenerateRepository extends BaseRepository
{
    public function index(Authenticatable $auth, int $page, int $size): Paginator
    {
        return TextGenerate::public()->where('user_id', $auth->id)->orderBy('id', 'desc')->paginate($size, '*', 'page', $page);
    }

    public function store(
        User $user,
        TextGenerateType $type,
        int $width,
        int $height,
        string $prompt,
        ?string $ethnicity,
        ?string $gender,
        ?int $age,
        bool $isSkinReality,
    ) {
        return TextGenerate::create(
            [
                'user_id'         => $user->id,
                'type_code'       => $type->value,
                'width'           => $width,
                'ratio'           => 'custom',
                'height'          => $height,
                'prompt'          => $prompt,
                'ethnicity'       => $ethnicity,
                'gender'          => $gender,
                'age'             => $age,
                'is_skin_reality' => $isSkinReality,
            ]);
    }

    public function setSuccess(int $id): TextGenerate
    {
        $model = TextGenerate::findOrFail($id);
        $model->is_complete = true;
        $model->save();
        return $model;
    }

    public function destroy(TextGenerate $generate): bool
    {
        return $generate->delete();
    }
}
