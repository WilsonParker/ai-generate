<?php

namespace App\Models\Stock\AI;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StockImage extends Media
{
    protected $connection = 'ai';

    public function getUrl(string $conversionName = ''): string
    {
        return $this->replacePath(parent::getUrl($conversionName));
    }

    public function getPath(string $conversionName = ''): string
    {
        return $this->replacePath(parent::getPath($conversionName));
    }

    private function replacePath(string $str): string
    {
        $path = Str::replace(config('constant.images.root'), config('media-library.ai_host'), $str);
        return Str::replace(config('media-library.prefix'), config('media-library.ai_prefix'), $path);
    }
}
