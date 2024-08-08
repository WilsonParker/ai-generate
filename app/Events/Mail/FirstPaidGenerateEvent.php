<?php

namespace App\Events\Mail;

/*
 * 첫 유료 generate 시 메일 발송 event
 * */

class FirstPaidGenerateEvent extends BaseEvent
{

    public function getEmailId(): int
    {
        return 20;
    }
}
