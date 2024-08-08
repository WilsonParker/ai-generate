<?php

namespace App\Events\Mail;

/*
 * 포인트 충전 시 메일 발송 event
 * */

class PointChargeEvent extends BaseEvent
{

    public function getEmailId(): int
    {
        return 21;
    }
}
