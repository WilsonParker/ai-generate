<?php

namespace App\Services\Image;

use App\Services\Image\Contracts\Imageable;
use App\Services\Image\Contracts\ImageableRepository;
use App\Services\Image\Contracts\ImageServiceContract;
use App\Services\Image\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService implements ImageServiceContract
{
    public function __construct(
        private readonly ImageableRepository $repository,
        private readonly string              $disk = 'public',
        private readonly string              $path = 'images',
    ) {}

    public function upload(Imageable $imageable, UploadedFile $file, string $mediaCollection = null): Image
    {
        $filename = Storage::disk($this->disk)->putFile($this->path, $file);
        $path = dirname($filename);
        $name = basename($filename);
        return $this->repository->store(
            $imageable,
            $path,
            $name,
            $file->getClientOriginalName(),
            $file->getSize(),
            $file->getMimeType()
        );
    }

    public function getLink(Image $image): string
    {
        return Storage::disk($this->disk)->url($image->path . '/' . $image->name);
    }
}
