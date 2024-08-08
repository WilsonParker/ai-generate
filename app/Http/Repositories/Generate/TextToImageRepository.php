<?php

namespace App\Http\Repositories\Generate;

use App\Http\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use AIGenerate\Models\User\User;
use AIGenerate\Services\Generate\Enums\SamplingMethod;
use AIGenerate\Services\Generate\Enums\TextToImageType;

class TextToImageRepository extends BaseRepository
{
    public function store(
        User $user,
        TextToImageType $type,
        string $prompt,
        ?string $negative,
        int $width,
        int $height,
        SamplingMethod $method = SamplingMethod::DPM_PP_2M_SDE_KARRAS,
        int $steps = 20,
        $cfgScale = 7.0,
        int $seed = -1,
        array $alwaysonScripts = [],
        string $payload = '',
    ) {
        return $this->model::create(
            [
                'user_id'          => $user->getKey(),
                'type_code'        => $type->value,
                'payload'          => $payload,
                'width'            => $width,
                'height'           => $height,
                'prompt'           => $prompt,
                'negative'         => $negative,
                'sampling_method'  => $method->value,
                'steps'            => $steps,
                'cfg_scale'        => $cfgScale,
                'seed'             => $seed,
                'alwayson_scripts' => json_encode($alwaysonScripts),
            ]);
    }

    public function index(User $user, int $page, int $size)
    {
        return $this->model::where('user_id', $user->id)->orderBy('id', 'desc')->paginate($size, '*', 'page', $page);
    }

    public function setSuccess(int $id): Model
    {
        $model = $this->model::findOrFail($id);
        $model->is_complete = true;
        $model->save();
        return $model;
    }

    public function destroy(Model $generate): bool
    {
        return $generate->delete();
    }
}
