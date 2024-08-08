<?php

namespace App\Events\Mail;

/*
 * 본인 상품 첫 5회 generate 시 메일 발송 event
 * */

class MyPrompt5TimesGeneratedEvent extends BaseEvent
{
    public function getEmailId(): int
    {
        return 12;
    }
}
