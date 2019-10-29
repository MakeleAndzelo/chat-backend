<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

class Message
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $body;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Channel
     */
    private $channel;

    /**
     * @var DateTime
     */
    private $createdAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getChannel(): Channel
    {
        return $this->channel;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setChannel(Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
