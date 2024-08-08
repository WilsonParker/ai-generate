<?php

namespace App\Listeners\Stock;


use App\Events\Stock\ShowStockEvent;
use App\Http\Repositories\Stock\StockViewRepository;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;

class ShowStockEventListener
{
    use InteractsWithQueue;

    public $afterCommit = true;

    public function __construct(private readonly StockViewRepository $repository)
    {
    }

    public function handle(ShowStockEvent $event): void
    {
        $this->repository->add($event->stock, $event->user);
    }

    /**
     * Handle a job failure.
     */
    public function failed(ShowStockEvent $event, Throwable $exception): void
    {
    }
}
