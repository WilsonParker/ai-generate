<?php

namespace App\Services\Image\Contracts;

interface ImageableRepository
{
    public function store(
        Imageable $imageable,
        string    $path,
        string    $name,
        string    $originName,
        string    $size,
        string    $mime
    ): ImageModel;
}
