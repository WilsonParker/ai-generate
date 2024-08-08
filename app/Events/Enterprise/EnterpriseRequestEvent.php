<?php

namespace App\Events\Enterprise;

use App\Models\Enterprise\EnterpriseRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnterpriseRequestEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels, Queueable;

    public function __construct(public EnterpriseRequest $enterpriseRequest)
    {
    }
}
