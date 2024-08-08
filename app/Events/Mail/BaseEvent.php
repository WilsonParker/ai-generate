<?php

namespace App\Events\Mail;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/*
 * 첫 유료 generate 시 메일 발송 event
 * */

abstract class BaseEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'ai_generate-mail';

    public function __construct(public int $userId) {}

    public function getUserId(): int
    {
        return $this->userId;
    }

    abstract public function getEmailId(): int;

}
