<?php

declare(strict_types=1);

namespace App\Server;

use Ratchet\ConnectionInterface;
use SplObjectStorage;

class UserConnectionsStorage implements UserConnectionsStorageInterface
{
    /**
     * @var SplObjectStorage|UserConnection[]
     */
    private $userConnections;

    public function __construct(SplObjectStorage $userConnections)
    {
        $this->userConnections = $userConnections;
    }

    /**
     * @return UserConnection[]|SplObjectStorage
     */
    public function getUserConnections()
    {
        return $this->userConnections;
    }

    public function attach(UserConnection $userConnection): void
    {
        $this->userConnections->attach($userConnection);
    }

    public function detach(UserConnection $userConnection): void
    {
        $this->userConnections->detach($userConnection);
    }

    public function findByConnection(ConnectionInterface $connection): ?UserConnection
    {
        $userSought = null;

        foreach ($this->userConnections as $userConnection) {
            if ($userConnection->getConnection() === $connection) {
                $userSought = $userConnection;
            }
        }

        return $userSought;
    }
}
