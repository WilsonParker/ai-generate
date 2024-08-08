<?php

namespace App\Services\Prompt\PromptDecorators;

use App\Services\Prompt\PromptDecorators\Contracts\PromptDecorator;

class LastOrderDecorator implements PromptDecorator
{
    public function decorate(string $message, array $attributes): string
    {
        if (isset($attributes['order'])) {
            $message .= "{$attributes['order']}";
        }
        return $message;
    }
}
