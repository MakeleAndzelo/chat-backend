<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Channel;
use App\Events\ChannelChangedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChangeChannelSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ChannelChangedEvent::class => 'onChannelChanged'
        ];
    }

    public function onChannelChanged(ChannelChangedEvent $event): void
    {
        $channelRepository = $this->entityManager->getRepository(Channel::class);
        $channel = $channelRepository->findOneBy(['id' => $event->getData()['channel']['id']]);
    }
}
