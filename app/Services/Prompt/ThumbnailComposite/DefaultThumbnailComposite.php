<?php

namespace App\Services\Prompt\ThumbnailComposite;


use App\Services\Image\Contracts\ImageServiceContract;
use App\Services\Image\Models\Image;
use App\Services\Prompt\ThumbnailComposite\Contracts\PickThumbnail;
use Illuminate\Http\UploadedFile;
use AIGenerate\Models\Prompt\Prompt;

class DefaultThumbnailComposite implements PickThumbnail
{
    public function __construct(
        private readonly ImageServiceContract $imageService,
    ) {}

    /**
     * @throws \Exception
     */
    function pick(Prompt $prompt): Image
    {
        $file = new UploadedFile(
            storage_path('app/test.jpg'),
            'test.jpg',
            'image/jpeg',
        );
        return $this->imageService->upload($prompt, $file);
    }

    function isCorrect(Prompt $prompt): bool
    {
        return true;
    }
}
