<?php

namespace App\Events\Mail;

/*
 * Point $2 미만일 시 메일 발송 event
 * */

class PointLessThanEvent extends BaseEvent
{

    public function getEmailId(): int
    {
        return 15;
    }
}
