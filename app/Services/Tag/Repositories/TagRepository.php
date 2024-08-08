<?php

namespace App\Services\Tag\Repositories;

use App\Services\Tag\Models\Tag;

class TagRepository
{
    public function firstOrCreate(string $name): Tag
    {
        return Tag::firstOrCreate(['name' => $name]);
    }
}
