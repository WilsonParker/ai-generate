<?php

namespace App\Events\Mail;

/*
 * 본인 상품 첫 generate 시 메일 발송 event
 * */

class MyPromptFirstGeneratedEvent extends BaseEvent
{
    public function getEmailId(): int
    {
        return 14;
    }
}
