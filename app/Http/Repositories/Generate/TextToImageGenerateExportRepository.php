<?php

namespace App\Http\Repositories\Generate;

use App\Http\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class TextToImageGenerateExportRepository extends BaseRepository
{
    public function store(Model $generate): Model
    {
        return $this->create(['text_to_image_generate_id' => $generate->id]);
    }
}
