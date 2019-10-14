<?php


namespace App\EventSubscriber;


use App\Entity\User;
use App\Events\ChatUserAuthorizationRequestedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChatUserAuthorizationSubscriber implements EventSubscriberInterface
{
    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTManager;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(JWTTokenManagerInterface $JWTManager, EntityManagerInterface $entityManager)
    {
        $this->JWTManager = $JWTManager;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ChatUserAuthorizationRequestedEvent::class => 'onUserAuthorizationRequested',
        ];
    }

    public function onUserAuthorizationRequested(ChatUserAuthorizationRequestedEvent $event)
    {
        $clients = $event->getClients();
        $data = $event->getData();
        $from = $event->getFrom();

        $token = new JWTUserToken();
        $token->setRawToken($data['token']);
        $payload = $this->JWTManager->decode($token);

        $userRepository = $this->entityManager->getRepository(User::class);

        $user = $userRepository->findOneBy([
            'email' => $payload['username']
        ]);

        if (!$user instanceof User) {
            return;
        }

        foreach ($clients as $client) {
            if ($client->getConnection() === $from) {
                $client->attachUser($user);
            }
        }

        $connectedUsersIds = [];

        foreach ($clients as $client) {
            $connectedUsersIds[] = $client->getUserId();

            if ($client->getUserId() !== $user->getId()) {
                $client->getConnection()->send(json_encode([
                    'type' => 'onlineUser',
                    'payload' => [
                        'id' => $user->getId(),
                        'name' => $user->getName(),
                        'online' => true
                    ]
                ]));
            }
        }

        $from->send(json_encode([
            'type' => 'onlineUsers',
            'payload' => [
                'ids' => $connectedUsersIds
            ]
        ]));
    }
}
