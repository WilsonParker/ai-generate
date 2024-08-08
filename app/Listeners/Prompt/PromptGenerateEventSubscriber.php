<?php

namespace App\Listeners\Prompt;


use App\Events\Prompt\FreePromptGeneratedEvent;
use App\Events\Prompt\PromptGeneratedEvent;
use App\Services\Mail\MailService;
use App\Services\Point\PointCalculator;
use App\Services\Point\PointService;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\User\User;

class PromptGenerateEventSubscriber
{
    use InteractsWithQueue;

    public $afterCommit = true;

    public function __construct(
        protected PointService $pointService,
        protected PointCalculator $pointCalculator,
        protected MailService $mailService,
    ) {}

    public function handlePromptGeneratedEvent(PromptGeneratedEvent $event): void
    {
        $promptGenerate = $event->getPromptGenerate();
        $pointToBePaid = $this->pointCalculator->getPointToBePaid($promptGenerate);
        $this->pointService->paidPoint(
            $promptGenerate->user,
            $pointToBePaid,
            "Generate prompt {$promptGenerate->prompt->title} #{$promptGenerate->getKey()}",
        );
        $this->addGenerated($promptGenerate->user, $promptGenerate->prompt);
        $this->mailService->dispatchPointLessThanEvent($promptGenerate->user);
        $this->mailService->dispatchPromptFirstGeneratedEvent($promptGenerate->prompt->user);
        $this->mailService->dispatchPrompt5TimesGeneratedEvent($promptGenerate->prompt->user);
        $this->mailService->dispatchReachGeneratedRevenueEvent($promptGenerate->prompt->user);
    }

    private function addGenerated(User $user, Prompt $prompt)
    {
        $user->count->increment('generates');
        $prompt->count->increment('generated');
        $prompt->user->count->increment('generated');
    }

    public function handleFreePromptGeneratedEvent(FreePromptGeneratedEvent $event): void
    {
        $promptGenerate = $event->getPromptGenerate();
        $this->pointService->paidPoint(
            $promptGenerate->user,
            0,
            "Generate free prompt {$promptGenerate->prompt->title} #{$promptGenerate->getKey()}",
        );
        $this->addGenerated($promptGenerate->user, $promptGenerate->prompt);
        $this->mailService->dispatchPromptFirstGeneratedEvent($promptGenerate->prompt->user);
        $this->mailService->dispatchPrompt5TimesGeneratedEvent($promptGenerate->prompt->user);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            PromptGeneratedEvent::class     => 'handlePromptGeneratedEvent',
            FreePromptGeneratedEvent::class => 'handleFreePromptGeneratedEvent',
        ];
    }

}
