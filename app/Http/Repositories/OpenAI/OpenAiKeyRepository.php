<?php

namespace App\Http\Repositories\OpenAI;

use App\Exceptions\OpentAI\NotFoundOpenAIStackException;
use App\Http\Repositories\BaseRepository;
use AIGenerate\Models\OpenAI\OpenAiKeyStack;

class OpenAiKeyRepository extends BaseRepository
{
    public function __construct(protected string $openAiKeyModel, protected string $openAiKeyStackModel) {}

    /**
     * @throws \Throwable
     */
    function getOpenAiKeyStackModel(string $date, ?OpenAiKeyStack $except = null): OpenAiKeyStack
    {
        $model = $this->openAiKeyStackModel::where('date', $date)
                                           ->when($except != null, function ($query) use ($except) {
                                               $query->where('id', '!=', $except->getKey());
                                           })
                                           ->orderBy('call')
                                           ->first();
        if ($model != null) {
            return $model;
        } else {
            $this->openAiKeyModel::all()->each(function ($key) use ($date, &$model) {
                $model = $this->openAiKeyStackModel::create([
                    'open_ai_key_id' => $key->id,
                    'call'           => 0,
                    'date'           => $date,
                ]);
            });
            throw_if($model == null, new NotFoundOpenAIStackException());
            return $model;
        }
    }

    function incrementCall(int $id): bool
    {
        return $this->openAiKeyStackModel::find($id)->increment('call');
    }
}
