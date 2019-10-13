<?php

declare(strict_types=1);

namespace App\Server;

use App\Entity\User;
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

        if ('userAuthorization' === $data['type']) {
            $jwtManager = $this->container->get('lexik_jwt_authentication.jwt_manager');

            $token = new JWTUserToken();
            $token->setRawToken($data['token']);
            $payload = $jwtManager->decode($token);

            $userRepository = $this
                ->container
                ->get('doctrine.orm.entity_manager')
                ->getRepository(User::class);

            $user = $userRepository->findOneBy([
                'email' => $payload['username']
            ]);

            if (!$user instanceof User) {
                return;
            }

            foreach ($this->clients as $client) {
                if ($client->getConnection() === $from) {
                    $client->attachUser($user);
                }
            }

            $connectedUsersIds = [];

            foreach ($this->clients as $client) {
                $connectedUsersIds[] = $client->getUserId();

                if ($client->getUserId() !== $user->getId()) {
                    $client->getConnection()->send(json_encode([
                        'type' => 'onlineUser',
                        'payload' => [
                            'id' => $user->getId(),
                            'name' => $user->getName(),
                            'online' => true
                        ]
                    ]));
                }
            }

            $from->send(json_encode([
                'type' => 'onlineUsers',
                'payload' => [
                    'ids' => $connectedUsersIds
                ]
            ]));
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
