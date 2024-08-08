<?php

namespace App\Services\Prompt\ThumbnailComposite;


use App\Services\Image\Models\Image;
use App\Services\Prompt\ThumbnailComposite\Contracts\PickThumbnail;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Services\AI\OpenAI\Enums\OpenAITypes;

class DallEThumbnailComposite implements PickThumbnail
{
    /**
     * @throws \Exception
     */
    function pick(Prompt $prompt): Image
    {
        return $prompt->images->first();
    }

    function isCorrect(Prompt $prompt): bool
    {
        return $prompt->getOpenAIType() === OpenAITypes::Image;
    }
}
