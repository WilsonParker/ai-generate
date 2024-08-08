<?php

namespace App\Services\Prompt\ThumbnailComposite\Contracts;

use App\Services\Image\Models\Image;
use AIGenerate\Models\Prompt\Prompt;

interface PickThumbnail
{
    function pick(Prompt $prompt): Image;

    function isCorrect(Prompt $prompt): bool;
}
