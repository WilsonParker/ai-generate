<?php

namespace App\Services\Prompt\PromptDecorators;

use App\Services\Prompt\PromptDecorators\Contracts\PromptDecorator;

class NullableAnswerDecorator implements PromptDecorator
{
    public function decorate(string $message, array $attributes): string
    {
        $template = "";

        if (isset($attributes['language'])) {
            $template .= "Write all in {$attributes['language']}. ";
        }
        if (isset($attributes['tone'])) {
            $template .= "Please write in {$attributes['language']} tone. ";
        }
        if (isset($attributes['writing_style'])) {
            $template .= "{$attributes['writing_style']} writing style.";
        }
        return $message . "\n" . $template;
    }
}
