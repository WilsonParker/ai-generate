<?php

namespace App\Listeners\Stock;


use App\Events\Brevo\UserUpdateEvent;
use App\Events\Stock\StockGeneratedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StockGenerateEventListener implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'ai_generate-stock';
    public $afterCommit = true;

    public function __construct() {}

    public function handle(StockGeneratedEvent $event): void
    {
        $event->data->stock->count->increment('generates');
        $event->data->user->count->increment('stock_generates');
        UserUpdateEvent::dispatch($event->data->user);
    }

}
