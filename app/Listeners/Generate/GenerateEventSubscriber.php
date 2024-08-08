<?php

namespace App\Listeners\Generate;


use App\Events\Brevo\UserUpdateEvent;
use App\Events\Stock\ImageToImageGeneratedEvent;
use App\Events\Stock\TextToImageGeneratedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;

class GenerateEventSubscriber implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'ai_generate-stock';
    public $afterCommit = true;

    public function handle($event): void
    {
        $event->data->user->count->increment('text_generates');
        UserUpdateEvent::dispatch($event->data->user);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            TextToImageGeneratedEvent::class  => 'handle',
            ImageToImageGeneratedEvent::class => 'handle',
        ];
    }

}
