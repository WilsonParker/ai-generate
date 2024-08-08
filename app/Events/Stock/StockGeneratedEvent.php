<?php

namespace App\Events\Stock;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use AIGenerate\Models\Stock\StockGenerate;

class StockGeneratedEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The name of the queue on which to place the broadcasting job.
     *
     * @var string
     */
    public $queue = 'ai_generate-stock';

    public function __construct(public StockGenerate $data) {}

    /**
     * Get the channel the event should broadcast on.
     */
    public function broadcastOn()
    {
        return 'users.' . $this->data->user_id;
    }

    public function broadcastAs(): string
    {
        return 'stock.generated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'title'       => $this->data->stock->title,
            'seo_keyword' => $this->data->stock->keyword,
            'images'      => $this->data->images->map(fn($item) => $item->getUrl()),
            'stock_id'    => $this->data->stock_id,
            'user_id'     => $this->data->user_id,
        ];
    }


}
