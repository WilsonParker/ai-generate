<?php

namespace App\Events\Mail;

/*
 * Buyer 회원가입 시 메일 발송 event
 * */

class BuyerJoinEvent extends BaseEvent
{

    public function getEmailId(): int
    {
        return 10;
    }

}
