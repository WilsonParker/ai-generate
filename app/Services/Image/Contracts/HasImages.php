<?php

namespace App\Services\Image\Contracts;


use Illuminate\Database\Eloquent\Collection;

interface HasImages
{
    public function getImages(): Collection;
}
