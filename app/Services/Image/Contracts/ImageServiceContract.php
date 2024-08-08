<?php

namespace App\Services\Image\Contracts;

use Illuminate\Http\UploadedFile;

interface ImageServiceContract
{

    public function upload(Imageable $imageable, UploadedFile $file, string $mediaCollection = null): ImageModel;
}
