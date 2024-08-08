<?php

namespace App\Events\Mail;

/*
 * Free generate 소진 시 메일 발송 event
 * */

class FreeGenerateCompleteEvent extends BaseEvent
{
    public function getEmailId(): int
    {
        return 19;
    }
}
