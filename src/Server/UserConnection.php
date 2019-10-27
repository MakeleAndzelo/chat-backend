<?php

declare(strict_types=1);

namespace App\Server;

use App\Entity\Channel;
use App\Entity\User;
use Ratchet\ConnectionInterface;

class UserConnection
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Channel
     */
    private $channel;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    public function attachUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUserId(): ?int
    {
        if (! $this->user instanceof User) {
            return null;
        }

        return $this->user->getId();
    }

    public function changeChannel(Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getChannel(): Channel
    {
        return $this->channel;
    }
}
