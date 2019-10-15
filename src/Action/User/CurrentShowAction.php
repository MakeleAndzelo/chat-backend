<?php

declare(strict_types=1);

namespace App\Action\User;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class CurrentShowAction
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(Security $security, TokenStorageInterface $tokenStorage)
    {
        $this->security = $security;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(): ?User
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }
}
