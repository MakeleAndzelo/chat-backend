<?php

declare(strict_types=1);

namespace App\Events;

use App\Server\UserConnectionsStorageInterface;
use Ratchet\ConnectionInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractChatEvent extends Event
{
    /**
     * @var UserConnectionsStorageInterface
     */
    protected $userConnectionsStorage;

    /**
     * @var ConnectionInterface
     */
    protected $from;

    public function __construct(UserConnectionsStorageInterface $userConnectionStorage, ConnectionInterface $from)
    {
        $this->userConnectionsStorage = $userConnectionStorage;
        $this->from = $from;
    }

    /**
     * @return UserConnectionsStorageInterface
     */
    public function getUserConnectionsStorage()
    {
        return $this->userConnectionsStorage;
    }

    /**
     * @return ConnectionInterface
     */
    public function getFrom(): ConnectionInterface
    {
        return $this->from;
    }
}
