<?php

namespace App\Listeners\Generate;


use App\Events\Brevo\UserUpdateEvent;
use App\Events\Stock\TextGeneratedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TextGenerateEventListener implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'ai_generate-stock';
    public $afterCommit = true;

    public function __construct() {}

    public function handle(TextGeneratedEvent $event): void
    {
        $event->data->user->count->increment('text_generates');
        UserUpdateEvent::dispatch($event->data->user);
    }

}
