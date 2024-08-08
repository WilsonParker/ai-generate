<?php

namespace App\Services\Image\Contracts;

interface HasDefaultThumbnail
{
    function getThumbnail(?ImageModel $image, ?string $default): string;
}
