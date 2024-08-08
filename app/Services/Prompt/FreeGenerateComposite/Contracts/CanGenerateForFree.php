<?php

namespace App\Services\Prompt\FreeGenerateComposite\Contracts;

interface CanGenerateForFree
{
    function isFree(): bool;
}
