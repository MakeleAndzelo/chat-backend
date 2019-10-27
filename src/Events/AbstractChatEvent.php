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

    public function getUserConnectionsStorage(): UserConnectionsStorageInterface
    {
        return $this->userConnectionsStorage;
    }

    public function getFrom(): ConnectionInterface
    {
        return $this->from;
    }
}
