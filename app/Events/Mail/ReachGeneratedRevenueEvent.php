<?php

namespace App\Events\Mail;

/*
 * 첫 매출 $10 달성 시 메일 발송 event
 * */

class ReachGeneratedRevenueEvent extends BaseEvent
{
    public function getEmailId(): int
    {
        return 16;
    }
}
