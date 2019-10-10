<?php

declare(strict_types=1);

namespace App\Server;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChatServer implements MessageComponentInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var SplObjectStorage
     */
    protected $clients;

    public function __construct(ContainerInterface $container)
    {
        $this->clients = new SplObjectStorage;
        $this->container = $container;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        var_dump($this->clients->count());
        echo "New connection!\n";
    }

    function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection has disconnected\n";
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }

    function onMessage(ConnectionInterface $from, $msg)
    {
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }
}
