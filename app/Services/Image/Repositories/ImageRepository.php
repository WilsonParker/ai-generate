<?php

namespace App\Services\Image\Repositories;

use App\Services\Image\Contracts\Imageable;
use App\Services\Image\Contracts\ImageableRepository;
use App\Services\Image\Contracts\ImageModel;
use App\Services\Image\Models\Image;

class ImageRepository implements ImageableRepository
{
    public function __construct(protected ImageModel $image) {}

    public function store(
        Imageable $imageable,
        string    $path,
        string    $name,
        string    $originName,
        string    $size,
        string    $mime
    ): ImageModel {
        return Image::create([
            'path' => $path,
            'name' => $name,
            'origin_name' => $originName,
            'size' => $size,
            'mime' => $mime,
            'imageable_type' => $imageable->getImageableType(),
            'imageable_id' => $imageable->getImageableId(),
        ]);
    }
}
