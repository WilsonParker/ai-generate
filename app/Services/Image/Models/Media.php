<?php

namespace App\Services\Image\Models;

use App\Services\Image\Contracts\ImageModel;
use Illuminate\Support\Facades\Storage;

class Media extends \Spatie\MediaLibrary\MediaCollections\Models\Media implements ImageModel
{
    protected $connection = 'api';
    protected $table = 'media';

    public function getLink(string $conversion = ''): string
    {
        return $this->getUrl($conversion);
    }

    public function getOriginalTemporaryUrl(string $conversionName = '', int $minute = 5): string
    {
        if ($conversionName != '') {
            $path = $this->getPath($conversionName);
        } else {
            $path = $this->getPath();
        }
        return Storage::disk('media')->temporaryUrl($path, now()->addMinutes($minute));
    }
}
