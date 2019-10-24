<?php

declare(strict_types=1);

namespace App\Server;

use Ratchet\ConnectionInterface;

interface UserConnectionsStorageInterface
{
    public function attach(UserConnection $userConnection): void;

    public function detach(UserConnection $userConnection): void;

    public function findByConnection(ConnectionInterface $connection): ?UserConnection;

    /**
     * @return UserConnection[]
     */
    public function getUserConnections(): array;
}
