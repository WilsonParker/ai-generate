<?php

namespace App\Http\Repositories\Generate;

use App\Http\Repositories\BaseRepository;
use App\Models\Generate\ImageToImageGenerate;
use App\Models\Generate\ImageToImageGenerateExport;

class ImageToImageGenerateExportRepository extends BaseRepository
{
    public function store(ImageToImageGenerate $generate): ImageToImageGenerateExport
    {
        return $this->create(['image_to_image_generate_id' => $generate->id]);
    }
}
