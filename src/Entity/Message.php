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

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Channel
     */
    public function getChannel(): Channel
    {
        return $this->channel;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param string $body
     * @return Message
     */
    public function setBody(string $body): Message
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param User $user
     * @return Message
     */
    public function setUser(User $user): Message
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param Channel $channel
     * @return Message
     */
    public function setChannel(Channel $channel): Message
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * @param DateTime $createdAt
     * @return Message
     */
    public function setCreatedAt(DateTime $createdAt): Message
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
