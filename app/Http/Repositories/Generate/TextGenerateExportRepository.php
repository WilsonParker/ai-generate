<?php

namespace App\Http\Repositories\Generate;

use App\Http\Repositories\BaseRepository;
use App\Models\Generate\TextGenerateExport;
use AIGenerate\Models\Generate\TextGenerate;

class TextGenerateExportRepository extends BaseRepository
{
    public function store(TextGenerate $textGenerate): TextGenerateExport
    {
        return $this->create(['text_generate_id' => $textGenerate->id]);
    }
}
