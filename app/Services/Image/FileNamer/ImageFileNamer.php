<?php

namespace App\Services\Image\FileNamer;

use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Support\FileNamer\FileNamer;

class ImageFileNamer extends FileNamer
{

    public function originalFileName(string $fileName): string
    {
        return pathinfo($fileName, PATHINFO_FILENAME);
    }

    public function conversionFileName(string $fileName, Conversion $conversion): string
    {
        $strippedFileName = pathinfo($fileName, PATHINFO_FILENAME);
        $converted = md5($strippedFileName);
        return "{$strippedFileName}-{$converted}";
    }

    public function responsiveFileName(string $fileName): string
    {
        return pathinfo(md5($fileName), PATHINFO_FILENAME);
    }
}
