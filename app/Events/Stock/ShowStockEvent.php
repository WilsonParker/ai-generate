<?php

namespace App\Events\Stock;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use AIGenerate\Models\Stock\Stock;
use AIGenerate\Models\User\User;

class ShowStockEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels, Queueable;

    /**
     * @param \AIGenerate\Models\Stock\Stock $stock
     */
    public function __construct(public readonly Stock $stock, public readonly User $user) {}

}
