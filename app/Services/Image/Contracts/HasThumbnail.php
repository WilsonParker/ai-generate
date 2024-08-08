<?php

namespace App\Services\Image\Contracts;


interface HasThumbnail
{
    public function getThumbnail(): ImageModel;
}
