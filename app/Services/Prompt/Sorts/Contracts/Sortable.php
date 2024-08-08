<?php

namespace App\Services\Prompt\Sorts\Contracts;

use App\Services\Prompt\Sorts\Enums\Sorts;

interface Sortable
{
    public function sort($query, Sorts $sort);
}
