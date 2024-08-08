<?php

namespace App\Http\Response;

use Illuminate\Pagination\LengthAwarePaginator;

class SimplePaginator extends LengthAwarePaginator
{
    private \Illuminate\Contracts\Pagination\Paginator $paginator;

    public function __construct() {}

    public function transfer(\Illuminate\Contracts\Pagination\Paginator $paginator): self
    {
        $this->paginator = $paginator;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'current_page' => $this->paginator->currentPage(),
            'items'        => $this->paginator->items->toArray(),
            'last_page'    => $this->paginator->end ?? 0,
            'per_page'     => $this->paginator->perPage(),
            'total'        => $this->paginator->count ?? 0,
        ];
    }
}
