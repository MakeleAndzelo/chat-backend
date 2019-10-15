<?php

declare(strict_types=1);

namespace App\Server;

use App\Entity\User;
use App\Events\ChatEventTypes;
use App\Events\ChatNewMessageSent;
use App\Events\ChatUserAuthorizationRequestedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChatServer implements MessageComponentInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var SplObjectStorage|UserConnection[]
     */
    protected $clients;

    public function __construct(ContainerInterface $container)
    {
        $this->clients = new SplObjectStorage;
        $this->container = $container;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach(new UserConnection($conn));
    }

    function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        $eventDispatcher = $this->container->get('event_dispatcher');

        switch($data['type']) {
            case ChatEventTypes::USER_AUTHORIZATION_TYPE:
                $eventDispatcher->dispatch(
                    new ChatUserAuthorizationRequestedEvent($this->clients, $from, $data)
                );
                break;
            case ChatEventTypes::NEW_MESSAGE:
                $eventDispatcher->dispatch(
                  new ChatNewMessageSent($this->clients, $from, $data)
                );
                break;
            default:
                break;
        }
    }


    function onClose(ConnectionInterface $conn)
    {
        $userId = null;

        foreach ($this->clients as $client) {
            if ($client->getConnection() === $conn) {
                $userId = $client->getUserId();
                $this->clients->detach($client);
            }
        }

        if (null !== $userId) {
            foreach ($this->clients as $client) {
                $client->getConnection()->send(json_encode([
                    'type' => 'offlineUser',
                    'payload' => [
                        'id' => $userId
                    ]
                ]));
            }
        }
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }
}
