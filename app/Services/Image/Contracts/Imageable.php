<?php

namespace App\Services\Image\Contracts;

interface Imageable
{
    public function getImageableId();

    public function getImageableType(): string;

}
