<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Events\ChatNewMessageSent;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChatNewMessageSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ChatNewMessageSent::class => 'onNewMessage'
        ];
    }

    public function onNewMessage(ChatNewMessageSent $event): void
    {
        $timestamp = (new DateTime())->format('Y-m-d H:i');

        foreach ($event->getClients() as $client) {
            $client->getConnection()->send(json_encode([
                'type' => 'messagesAdd',
                'payload' => [
                    'message' => $event->getData()['message'],
                    'author' => 'foo',
                    'createdAt' => $timestamp
                ]
            ]));
        }
    }
}
