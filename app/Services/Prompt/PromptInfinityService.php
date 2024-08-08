<?php

namespace App\Services\Prompt;

use App\Services\Prompt\Sorts\Enums\Sorts;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

class PromptInfinityService extends PromptService
{
    public function index(
        string $search = null,
        Sorts  $sorts = Sorts::Newest,
        int    $page = 0,
        int    $size = 10,
        int    $last = 0,
        array  $types = [],
        array  $categories = [],
    ): Paginator|Collection {
        return $this->repository->index([
            'search' => $search,
            'sort' => $sorts,
            'page' => $page,
            'size' => $size,
            'last' => $last,
            'types' => $types,
            'categories' => $categories,
        ], function ($query) use ($sorts) {
            $this->sorts->sort($query, $sorts);
        });
    }
}
