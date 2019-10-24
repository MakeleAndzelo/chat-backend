<?php

declare(strict_types=1);

namespace App\Services;

use App\Exception\UserConnectionNotFoundException;
use App\Server\UserConnection;
use App\Server\UserConnectionsStorage;
use Ratchet\ConnectionInterface;

class ClientRemover
{
    /**
     * @throws UserConnectionNotFoundException
     */
    public function remove(
        UserConnectionsStorage $userConnectionsStorage,
        ConnectionInterface $connection
    ): UserConnection {
        $userConnection = $userConnectionsStorage->findByConnection($connection);

        if (! $userConnection instanceof UserConnection) {
            throw new UserConnectionNotFoundException();
        }

        $userConnectionsStorage->detach($userConnection);

        return $userConnection;
    }
}
