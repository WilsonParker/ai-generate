<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class StripeBaseResources extends JsonResource
{
    public function toArray(Request $request)
    {
        return $this->fields($request);
    }

    abstract function fields(Request $request): array;
}
