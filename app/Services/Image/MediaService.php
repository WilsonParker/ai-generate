<?php

namespace App\Services\Image;

use App\Services\Image\Contracts\Imageable;
use App\Services\Image\Contracts\ImageableRepository;
use App\Services\Image\Contracts\ImageModel;
use App\Services\Image\Contracts\ImageServiceContract;
use Illuminate\Http\UploadedFile;

class MediaService implements ImageServiceContract
{
    public function __construct(
        private readonly ImageableRepository $repository,
        private readonly string              $disk = 'public',
        private readonly string              $path = 'images',
    ) {}

    public function upload(Imageable $imageable, UploadedFile $file, string $mediaCollection = null): ImageModel
    {
        return $imageable->addMedia($file)
                         ->usingFileName(
                             md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension()
                         )
                         ->toMediaCollection($mediaCollection, $this->disk);
    }

    public function getLink(ImageModel $image): string
    {
        return $image->getLink();
    }
}
