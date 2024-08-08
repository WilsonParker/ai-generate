<?php

namespace App\Http\Repositories\Prompt\Contracts;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use AIGenerate\Models\Prompt\Prompt;

interface PromptRepositoryContract
{

    public function index(array $attributes, callable $sortCallback): Paginator|Collection;

    public function delete(Prompt $prompt): bool;

    public function main(): Collection;


}
