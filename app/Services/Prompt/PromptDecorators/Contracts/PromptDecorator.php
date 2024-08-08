<?php

namespace App\Services\Prompt\PromptDecorators\Contracts;

interface PromptDecorator
{
    public function decorate(string $message, array $attributes): string;

}
