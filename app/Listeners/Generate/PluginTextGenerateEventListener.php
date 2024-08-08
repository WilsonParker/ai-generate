<?php

namespace App\Listeners\Generate;


use App\Events\Stock\PluginTextGeneratedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PluginTextGenerateEventListener implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'ai_generate-stock';
    public $afterCommit = true;

    public function __construct() {}

    public function handle(PluginTextGeneratedEvent $event): void
    {
        $event->data->user->count->increment('text_generates');
    }

}
