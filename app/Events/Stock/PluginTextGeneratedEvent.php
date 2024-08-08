<?php

namespace App\Events\Stock;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use AIGenerate\Models\Generate\TextGenerate;

class PluginTextGeneratedEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The name of the queue on which to place the broadcasting job.
     *
     * @var string
     */
    public $queue = 'ai_generate-stock';

    public function __construct(public TextGenerate $data, public string $key) {}

    /**
     * Get the channel the event should broadcast on.
     */
    public function broadcastOn()
    {
        return 'plugin.text.' . $this->key;
    }

    public function broadcastAs(): string
    {
        return 'plugin.text.generated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'prompt'  => $this->data->prompt,
            'user_id' => $this->data->user_id,
            'images'  => $this->data->images?->map(fn($item) => $item->getUrl()),
        ];
    }


}
