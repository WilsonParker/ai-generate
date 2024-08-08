<?php

namespace App\Events\Prompt;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\User\User;

class ShowPromptEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels, Queueable;

    /**
     * @param \AIGenerate\Models\Prompt\Prompt $prompt
     */
    public function __construct(public readonly Prompt $prompt, public readonly User $user) {}

}
