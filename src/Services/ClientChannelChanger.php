<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Channel;
use App\Server\UserConnection;
use Ratchet\ConnectionInterface;
use SplObjectStorage;

class ClientChannelChanger
{
    /**
     * @param SplObjectStorage|UserConnection[] $clients
     * @param ConnectionInterface $from
     * @param Channel $channel
     *
     * @return SplObjectStorage
     */
    public function change(SplObjectStorage $clients, ConnectionInterface $from, Channel $channel): SplObjectStorage
    {
        foreach ($clients as $client) {
            if ($client->getConnection() === $from) {
                $client->changeChannel($channel);
            }
        }

        return $clients;
    }
}
