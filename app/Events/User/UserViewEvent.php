<?php

namespace App\Events\User;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use AIGenerate\Models\User\User;

class UserViewEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param \AIGenerate\Models\User\User $from
     * @param \AIGenerate\Models\User\User $to
     */
    public function __construct(public User $from, public User $to) {}

}
