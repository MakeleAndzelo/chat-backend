<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Channel;
use App\Entity\User;
use App\Server\UserConnectionsStorage;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Ratchet\ConnectionInterface;

class ClientAuthorizer
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTTokenManager;

    public function __construct(EntityManagerInterface $entityManager, JWTTokenManagerInterface $JWTTokenManager)
    {
        $this->entityManager = $entityManager;
        $this->JWTTokenManager = $JWTTokenManager;
    }

    public function authorize(
        UserConnectionsStorage $userConnectionsStorage,
        ConnectionInterface $from,
        string $rawToken,
        int $channelId
    ): void {
        $token = new JWTUserToken();
        $token->setRawToken($rawToken);
        $payload = $this->JWTTokenManager->decode($token);

        $userRepository = $this->entityManager->getRepository(User::class);
        $channelRepository = $this->entityManager->getRepository(Channel::class);

        $channel = $channelRepository->findOneBy(['id' => $channelId]);

        $user = $userRepository->findOneBy([
            'email' => $payload['username'],
        ]);

        if (! $user instanceof User) {
            return;
        }

        foreach ($userConnectionsStorage->getUserConnections() as $client) {
            if ($client->getConnection() === $from) {
                $client->attachUser($user);
                $client->changeChannel($channel);
            }
        }
    }
}
