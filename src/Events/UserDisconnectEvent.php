<?php

declare(strict_types=1);

namespace App\Events;

use App\Server\UserConnection;
use App\Server\UserConnectionsStorage;
use Symfony\Contracts\EventDispatcher\Event;

class UserDisconnectEvent extends Event
{
    /**
     * @var UserConnectionsStorage
     */
    private $userConnectionsStorage;

    /**
     * @var UserConnection
     */
    private $removedUserConnection;

    public function __construct(UserConnectionsStorage $userConnectionsStorage, UserConnection $removedUserConnection)
    {
        $this->userConnectionsStorage = $userConnectionsStorage;
        $this->removedUserConnection = $removedUserConnection;
    }

    /**
     * @return UserConnectionsStorage
     */
    public function getUserConnectionsStorage(): UserConnectionsStorage
    {
        return $this->userConnectionsStorage;
    }

    /**
     * @return UserConnection
     */
    public function getRemovedUserConnection(): UserConnection
    {
        return $this->removedUserConnection;
    }
}
