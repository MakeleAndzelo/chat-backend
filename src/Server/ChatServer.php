<?php

declare(strict_types=1);

namespace App\Server;

use App\Entity\Channel;
use App\Events\ChatEventTypes;
use App\Events\ChatNewMessageSent;
use App\Events\ChatUserAuthorizationRequestedEvent;
use App\Events\UserDisconnectEvent;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ChatServer implements MessageComponentInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var UserConnectionsStorage
     */
    protected $userConnectionsStorage;

    public function __construct(ContainerInterface $container)
    {
        $this->userConnectionsStorage = new UserConnectionsStorage(new SplObjectStorage());
        $this->container = $container;
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->userConnectionsStorage->attach(new UserConnection($conn));
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $data = json_decode($msg, true);
        $eventDispatcher = $this->container->get('event_dispatcher');

        switch($data['type']) {
            case ChatEventTypes::USER_AUTHORIZATION_TYPE:
                $clientAuthorizer = $this->container->get('app.chat.client_authorizer');
                $clientAuthorizer->authorize($this->userConnectionsStorage, $from, $data['token'], $data['channel']['id']);

                $eventDispatcher->dispatch(
                    new ChatUserAuthorizationRequestedEvent($this->userConnectionsStorage, $from, $data)
                );
                break;
            case ChatEventTypes::NEW_MESSAGE:
                $eventDispatcher->dispatch(
                  new ChatNewMessageSent($this->userConnectionsStorage, $from, $data)
                );
                break;
            case ChatEventTypes::CHANNEL_CHANGE:
                $entityManager = $this->container->get('doctrine.orm.entity_manager');
                $clientChannelChanger = $this->container->get('app.chat.client_channel_changer');
                $channelRepository = $entityManager->getRepository(Channel::class);
                $channel = $channelRepository->findOneBy(['id' => $data['channel']['id']]);

                if ($channel instanceof Channel) {
                    $clientChannelChanger->change(
                        $this->userConnectionsStorage,
                        $from,
                        $channel
                    );
                }
                break;
            default:
                break;
        }
    }


    public function onClose(ConnectionInterface $conn): void
    {
        $clientRemover = $this->container->get('app.chat.client_remover');
        $removedUserConnection = $clientRemover->remove($this->userConnectionsStorage, $conn);

        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(new UserDisconnectEvent($this->userConnectionsStorage, $removedUserConnection));
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        $conn->send(json_encode([
            'type' => 'connectionError'
        ]));
    }
}
