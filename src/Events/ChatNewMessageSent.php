<?php

declare(strict_types=1);

namespace App\Events;

use App\Server\UserConnectionsStorageInterface;
use Ratchet\ConnectionInterface;

class ChatNewMessageSent extends AbstractChatEvent
{
    /**
     * @var array
     */
    private $data = [];

    public function __construct(
        UserConnectionsStorageInterface $userConnectionStorage,
        ConnectionInterface $from,
        array $data
    ) {
        parent::__construct($userConnectionStorage, $from);
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
