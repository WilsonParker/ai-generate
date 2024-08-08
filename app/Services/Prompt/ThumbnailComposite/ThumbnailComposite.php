<?php

namespace App\Services\Prompt\ThumbnailComposite;


use App\Services\Image\Models\Image;
use App\Services\Prompt\ThumbnailComposite\Contracts\PickThumbnail;
use Exception;
use AIGenerate\Models\Prompt\Prompt;

class ThumbnailComposite implements PickThumbnail
{
    public function __construct(
        /**
         * @var PickThumbnail[]
         **/
        private readonly array $composites,
    ) {}

    /**
     * @throws \Exception
     */
    function pick(Prompt $prompt): Image
    {
        foreach ($this->composites as $composite) {
            if ($composite->isCorrect($prompt)) {
                return $composite->pick($prompt);
            }
        }
        throw new Exception('No Thumbnail');
    }

    function isCorrect(Prompt $prompt): bool
    {
        foreach ($this->composites as $composite) {
            if ($composite->isCorrect($prompt)) {
                return true;
            }
        }
        return false;
    }
}
