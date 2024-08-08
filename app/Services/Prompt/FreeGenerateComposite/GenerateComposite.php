<?php

namespace App\Services\Prompt\FreeGenerateComposite;

use App\Services\Prompt\FreeGenerateComposite\Contracts\CanGenerateForFree;

class GenerateComposite implements CanGenerateForFree
{
    public function __construct(
        /**
         * @var CanGenerateForFree[]
         **/
        private readonly array $composites
    ) {}

    function isFree(): bool
    {
        foreach ($this->composites as $item) {
            if ($item->isFree()) {
                return true;
            }
        }
        return false;
    }

}
