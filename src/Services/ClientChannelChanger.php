<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Channel;
use App\Server\UserConnection;
use App\Server\UserConnectionsStorageInterface;
use Ratchet\ConnectionInterface;

class ClientChannelChanger
{
    /**
     * @param UserConnectionsStorageInterface $userConnectionsStorage
     * @param ConnectionInterface $from
     * @param Channel $channel
     */
    public function change(
        UserConnectionsStorageInterface $userConnectionsStorage,
        ConnectionInterface $from,
        Channel $channel
    ): void {
        $userConnection = $userConnectionsStorage->findByConnection($from);

        if ($userConnection instanceof UserConnection) {
            $userConnection->changeChannel($channel);
        }
    }
}
