<?php

declare(strict_types=1);

namespace App\Command;

use App\Server\ChatServer;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this->setName('chat:server');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $server = IoServer::factory(new HttpServer(
            new WsServer(
                new ChatServer($this->getContainer())
            )
        ), 8002);

        $server->run();
    }
}
