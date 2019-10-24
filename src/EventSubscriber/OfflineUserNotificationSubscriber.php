<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Events\UserDisconnectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OfflineUserNotificationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [UserDisconnectEvent::class => 'onUserDisconnect'];
    }

    public function onUserDisconnect(UserDisconnectEvent $event): void
    {
        $userConnectionsStorage = $event->getUserConnectionsStorage();
        $removedUserConnection = $event->getRemovedUserConnection();

        foreach ($userConnectionsStorage->getUserConnections() as $client) {
            $client->getConnection()->send(json_encode([
                'type' => 'offlineUser',
                'payload' => [
                    'id' => $removedUserConnection->getUserId()
                ]
            ]));
        }
    }
}
