<?php

namespace App\Services\Prompt\Sorts;

use App\Services\Prompt\Sorts\Contracts\Sortable;
use App\Services\Prompt\Sorts\Enums\Sorts;

class TopSort implements Sortable
{

    public function sort($query, Sorts $sort)
    {
        if ($sort == Sorts::TOP) {
            $query->orderBy('top', 'desc');
        }
    }
}
