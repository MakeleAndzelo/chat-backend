<?php

namespace App\Events;

use App\Server\UserConnection;
use Ratchet\ConnectionInterface;
use SplObjectStorage;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractChatEvent extends Event
{
    /**
     * @var SplObjectStorage|UserConnection[]
     */
    protected $clients;

    /**
     * @var ConnectionInterface
     */
    protected $from;

    public function __construct(SplObjectStorage $clients, ConnectionInterface $from)
    {
        $this->clients = $clients;
        $this->from = $from;
    }

    /**
     * @return UserConnection[]|SplObjectStorage
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * @return ConnectionInterface
     */
    public function getFrom(): ConnectionInterface
    {
        return $this->from;
    }
}
