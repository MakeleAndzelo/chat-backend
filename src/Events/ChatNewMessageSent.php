<?php

declare(strict_types=1);

namespace App\Events;

use Ratchet\ConnectionInterface;
use SplObjectStorage;

class ChatNewMessageSent extends AbstractChatEvent
{
    /**
     * @var array
     */
    protected $data;

    public function __construct(SplObjectStorage $clients, ConnectionInterface $from, array $data)
    {
        parent::__construct($clients, $from);
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
