<?php

namespace App\Listeners\Prompt;


use App\Events\Prompt\ShowPromptEvent;
use App\Http\Repositories\Prompt\PromptViewRepository;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;

class ShowPromptEventListener
{
    use InteractsWithQueue;

    public $afterCommit = true;

    public function __construct(private readonly PromptViewRepository $repository) {}

    public function handle(ShowPromptEvent $event): void
    {
        $this->repository->add($event->prompt, $event->user);
    }

    /**
     * Handle a job failure.
     */
    public function failed(ShowPromptEvent $event, Throwable $exception): void {}
}
