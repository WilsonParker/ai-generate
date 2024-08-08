<?php

namespace App\Services\Tag;

use Illuminate\Support\Collection;

class TagService
{

    public function __construct(protected Repositories\TagRepository $tagRepository) {}

    public function create(array|string $name): Models\Tag|Collection
    {
        if (is_array($name)) {
            return collect($name)->map(fn($name) => $this->tagRepository->firstOrCreate($name));
        } else {
            return $this->tagRepository->firstOrCreate($name['name']);
        }
    }

}
