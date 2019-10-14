<?php

declare(strict_types=1);

namespace App\Events;

use App\Server\UserConnection;
use Ratchet\ConnectionInterface;
use SplObjectStorage;
use Symfony\Contracts\EventDispatcher\Event;

class ChatUserAuthorizationRequestedEvent extends Event
{
    /**
     * @var SplObjectStorage|UserConnection[]
     */
    protected $clients;

    /**
     * @var ConnectionInterface
     */
    protected $from;

    /**
     * @var array
     */
    protected $data;

    public function __construct(SplObjectStorage $clients, ConnectionInterface $from, array $data)
    {
        $this->clients = $clients;
        $this->from = $from;
        $this->data = $data;
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

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
