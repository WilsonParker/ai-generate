<?php

namespace App\Services\Prompt\PromptDecorators;

use App\Services\Prompt\PromptDecorators\Contracts\PromptDecorator;

class Decorators
{
    /**
     * @param array $decorators
     */
    public function __construct(private array $decorators = []) {}

    public function addDecorator(PromptDecorator $decorator): void
    {
        $this->decorators[] = $decorator;
    }

    public function decorate(string $message, array $attributes): string
    {
        foreach ($this->decorators as $decorator) {
            $message = $decorator->decorate($message, $attributes);
        }

        return $message;
    }
}
