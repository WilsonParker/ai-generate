<?php

namespace App\Providers;

use App\Events\Prompt\ShowPromptEvent;
use App\Events\Stock\PluginTextGeneratedEvent;
use App\Events\Stock\ShowStockEvent;
use App\Events\Stock\StockGeneratedEvent;
use App\Events\Stock\TextGeneratedEvent;
use App\Listeners\Brevo\BrevoEventSubscriber;
use App\Listeners\Enterprise\EnterpriseEventSubscriber;
use App\Listeners\Generate\GenerateEventSubscriber;
use App\Listeners\Generate\PluginTextGenerateEventListener;
use App\Listeners\Generate\TextGenerateEventListener;
use App\Listeners\Mail\MailEventSubscriber;
use App\Listeners\Prompt\PromptGenerateEventSubscriber;
use App\Listeners\Prompt\ShowPromptEventListener;
use App\Listeners\SellerPayout\SellerPayoutEventSubscriber;
use App\Listeners\Stock\ShowStockEventListener;
use App\Listeners\Stock\StockGenerateEventListener;
use App\Listeners\User\UserEventSubscriber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class               => [
            SendEmailVerificationNotification::class,
        ],
        ShowPromptEvent::class          => [
            ShowPromptEventListener::class,
        ],
        ShowStockEvent::class           => [
            ShowStockEventListener::class,
        ],
        StockGeneratedEvent::class      => [
            StockGenerateEventListener::class,
        ],
        TextGeneratedEvent::class       => [
            TextGenerateEventListener::class,
        ],
        PluginTextGeneratedEvent::class => [
            PluginTextGenerateEventListener::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        UserEventSubscriber::class,
        PromptGenerateEventSubscriber::class,
        MailEventSubscriber::class,
        SellerPayoutEventSubscriber::class,
        BrevoEventSubscriber::class,
        EnterpriseEventSubscriber::class,
        GenerateEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
