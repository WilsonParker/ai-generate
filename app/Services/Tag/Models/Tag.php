<?php

namespace App\Services\Tag\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use AIGenerate\Models\Prompt\Prompt;

class Tag extends Model
{
    protected $table = 'tags';

    protected $fillable = [
        'name',
    ];

    public function prompts(): MorphToMany
    {
        return $this->morphedByMany(Prompt::class, 'taggable');
    }
}
