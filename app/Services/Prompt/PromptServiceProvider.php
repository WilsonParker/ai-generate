<?php

namespace App\Services\Prompt;

use App\Services\Prompt\PromptDecorators\AnswerDecorator;
use App\Services\Prompt\PromptDecorators\LastOrderDecorator;
use App\Services\Prompt\PromptDecorators\NullableAnswerDecorator;
use App\Services\Prompt\Sorts\NewestSort;
use App\Services\Prompt\Sorts\OldestSort;
use Illuminate\Support\ServiceProvider;

class PromptServiceProvider extends ServiceProvider
{

    public function register()
    {
        // PROMPT DECORATORS
        $this->app->singleton(LastOrderDecorator::class, fn() => new LastOrderDecorator());
        $this->app->singleton(AnswerDecorator::class, fn() => new AnswerDecorator());
        $this->app->singleton(NullableAnswerDecorator::class, fn() => new NullableAnswerDecorator());

        // PROMPT SORTS
        $this->app->singleton(NewestSort::class, fn() => new NewestSort());
        $this->app->singleton(OldestSort::class, fn() => new OldestSort());
    }
}
