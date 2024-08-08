<?php

namespace App\Services\Prompt\PromptDecorators;

use App\Services\Prompt\PromptDecorators\Contracts\PromptDecorator;

class AnswerDecorator implements PromptDecorator
{
    public function decorate(string $message, array $attributes): string
    {
        $template = "Write all in [language], Please write in [tone] tone, [writing_style] writing style.";

        $language = $attributes['language'] ?? 'english';
        $tone = $attributes['tone'] ?? 'cold';
        $style = $attributes['writing_style'] ?? 'descriptive';

        $template = str_replace('[language]', $language, $template);
        $template = str_replace('[tone]', $tone, $template);
        $template = str_replace('[writing_style]', $style, $template);
        return $message . "\n" . $template;
    }
}
