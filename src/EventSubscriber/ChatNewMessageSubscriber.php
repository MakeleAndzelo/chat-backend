<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Message;
use App\Events\ChatNewMessageSent;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ChatNewMessageSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ChatNewMessageSent::class => 'onNewMessage'
        ];
    }

    public function onNewMessage(ChatNewMessageSent $event): void
    {
        $currentClient = $event->getUserConnectionsStorage()->findByConnection($event->getFrom());

        $message = (new Message())
            ->setCreatedAt(new DateTime())
            ->setBody($event->getData()['message'])
            ->setUser($currentClient->getUser())
            ->setChannel($currentClient->getChannel());

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        foreach ($event->getUserConnectionsStorage()->getUserConnections() as $client) {
            if ($client->getChannel()->getId() === $currentClient->getChannel()->getId()) {
                $client->getConnection()->send($this->serializer->serialize([
                    'type' => 'messagesAdd',
                    'payload' => $message
                ], 'json'));
            }
        }
    }
}
