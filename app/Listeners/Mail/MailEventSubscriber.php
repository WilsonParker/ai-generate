<?php

namespace App\Listeners\Mail;


use App\Events\Mail\BuyerJoinEvent;
use App\Events\Mail\FirstPaidGenerateEvent;
use App\Events\Mail\FreeGenerateCompleteEvent;
use App\Events\Mail\MyPrompt5TimesGeneratedEvent;
use App\Events\Mail\MyPromptFirstGeneratedEvent;
use App\Events\Mail\PointChargeEvent;
use App\Events\Mail\PointLessThanEvent;
use App\Events\Mail\ReachGeneratedRevenueEvent;
use App\Events\Mail\SellerJoinEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;

class MailEventSubscriber implements ShouldQueue
{
    use InteractsWithQueue;

    public $afterCommit = true;

    public function __construct() {}

    public function handleSellerJoinEvent(SellerJoinEvent $event): void {}

    public function handleBuyerJoinEvent(BuyerJoinEvent $event): void {}

    public function handleFreeGenerateCompleteEvent(FreeGenerateCompleteEvent $event): void {}

    public function handleFirstPaidGenerateEvent(FirstPaidGenerateEvent $event): void {}

    public function handlePointLessThanEvent(PointLessThanEvent $event): void {}

    public function handlePointChargeEvent(PointChargeEvent $event): void {}

    public function handleMyPromptFirstGeneratedEvent(MyPromptFirstGeneratedEvent $event): void {}

    public function handleMyPrompt5TimesGeneratedEvent(MyPrompt5TimesGeneratedEvent $event): void {}

    public function handleReachGeneratedRevenueEvent(ReachGeneratedRevenueEvent $event): void {}


    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            SellerJoinEvent::class => 'handleSellerJoinEvent',
            BuyerJoinEvent::class => 'handleBuyerJoinEvent',
            FreeGenerateCompleteEvent::class => 'handleFreeGenerateCompleteEvent',
            FirstPaidGenerateEvent::class => 'handleFirstPaidGenerateEvent',
            PointLessThanEvent::class => 'handlePointLessThanEvent',
            PointChargeEvent::class => 'handlePointChargeEvent',
            MyPromptFirstGeneratedEvent::class => 'handleMyPromptFirstGeneratedEvent',
            MyPrompt5TimesGeneratedEvent::class => 'handleMyPrompt5TimesGeneratedEvent',
            ReachGeneratedRevenueEvent::class => 'handleReachGeneratedRevenueEvent',
        ];
    }

}
