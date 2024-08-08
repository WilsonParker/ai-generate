<?php

namespace App\Listeners\User;


use App\Events\User\UserViewEvent;
use App\Http\Repositories\User\UserViewRepository;
use Illuminate\Events\Dispatcher;

class UserEventSubscriber
{
    public $afterCommit = true;

    public function __construct(private readonly UserViewRepository $repository) {}

    public function handleUserViewEvent(UserViewEvent $event): void
    {
        $this->repository->add($event->from, $event->to);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            UserViewEvent::class => 'handleUserViewEvent',
        ];
    }

}
