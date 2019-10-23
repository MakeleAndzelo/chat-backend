<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Events\ChatUserAuthorizationRequestedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InformAboutOnlineUserSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ChatUserAuthorizationRequestedEvent::class => 'sendInfoAboutOnlineUser'
        ];
    }

    public function sendInfoAboutOnlineUser(ChatUserAuthorizationRequestedEvent $event)
    {
        $newOnlineUser = $event->getUserConnectionsStorage()->findByConnection($event->getFrom());

        foreach ($event->getUserConnectionsStorage()->getUserConnections() as $userConnection) {
            if ($userConnection->getUser() !== $newOnlineUser->getUser()) {
                $userConnection->getConnection()->send(json_encode([
                    'type' => 'onlineUser',
                    'payload' => [
                        'id' => $newOnlineUser->getUser()->getId(),
                        'name' => $newOnlineUser->getUser()->getName(),
                        'online' => true
                    ]
                ]));
            }
        }
    }
}
