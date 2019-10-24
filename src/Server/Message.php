<?php

declare(strict_types=1);

namespace App\Server;

class Message
{
    /**
     * @var string
     */
    private $type;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
