<?php

namespace App\Events\Prompt;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use AIGenerate\Models\Prompt\PromptGenerate;

/*
 * 무료로 prompt generate 가 되었을 때 발생하는 이벤트
 * */

class FreePromptGeneratedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param \AIGenerate\Models\Prompt\PromptGenerate $promptGenerate
     */
    public function __construct(private readonly PromptGenerate $promptGenerate) {}


    public function getPromptGenerate(): PromptGenerate
    {
        return $this->promptGenerate;
    }
}
