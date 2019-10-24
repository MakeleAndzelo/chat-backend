<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Events\ChatUserAuthorizationRequestedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChatUserAuthorizationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ChatUserAuthorizationRequestedEvent::class => 'onUserAuthorizationRequested',
        ];
    }

    public function onUserAuthorizationRequested(ChatUserAuthorizationRequestedEvent $event): void
    {
        $clients = $event->getUserConnectionsStorage();
        $from = $event->getFrom();
        $connectedUsersIds = [];

        foreach ($clients->getUserConnections() as $client) {
            $connectedUsersIds[] = $client->getUserId();
        }

        $from->send(json_encode([
            'type' => 'onlineUsers',
            'payload' => [
                'ids' => $connectedUsersIds,
            ],
        ]));
    }
}
