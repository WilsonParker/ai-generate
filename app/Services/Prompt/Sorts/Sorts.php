<?php

namespace App\Services\Prompt\Sorts;

use App\Services\Prompt\Sorts\Contracts\Sortable;
use Illuminate\Database\Query\Builder;

class Sorts
{
    public function __construct(private array $sorts = []) {}

    public function addSort(Sortable $sort): void
    {
        $this->sorts[] = $sort;
    }

    public function sort(Builder|\Laravel\Scout\Builder $query, Enums\Sorts $sorts): void
    {
        foreach ($this->sorts as $sort) {
            $sort->sort($query, $sorts);
        }
    }
}
