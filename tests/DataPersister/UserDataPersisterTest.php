<?php

declare(strict_types=1);

namespace App\Tests\DataPersister;

use App\DataPersister\UserDataPersister;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserDataPersisterTest extends TestCase
{
    /**
     * @test
     */
    public function itSupportsOnlyAUserEntityClass(): void
    {
        $userMock = $this->createUserMock();

        $entityManagerMock = $this->createEntityManagerMock();

        $userPasswordEncoderMock = $this->createUserPasswordEncoderMock();

        $userDataPersister = new UserDataPersister($entityManagerMock, $userPasswordEncoderMock);

        $this->assertTrue($userDataPersister->supports($userMock));
        $this->assertFalse($userDataPersister->supports($entityManagerMock));
    }

    /**
     * @test
     */
    public function itEncodeUserPasswordOnPersisting(): void
    {
        $userMockPassword = 'testingPassword';

        $userMock = $this->createUserMock();

        $userMock->expects($this->once())
            ->method('setPassword')
            ->willReturn($userMock);

        $userMock->expects($this->once())
            ->method('getPassword')
            ->willReturn($userMockPassword);

        $entityManagerMock = $this->createEntityManagerMock();

        $entityManagerMock->expects(self::once())
            ->method('persist');

        $entityManagerMock->expects(self::once())
            ->method('flush');

        $userPasswordEncoderMock = $this->createUserPasswordEncoderMock();

        $userPasswordEncoderMock->expects(self::once())
            ->method('encodePassword')
            ->with($userMock)
            ->willReturn('foobarbaz');

        $userDataPersister = new UserDataPersister($entityManagerMock, $userPasswordEncoderMock);
        $userDataPersister->persist($userMock);
    }

    /**
     * @return User|MockObject
     */
    private function createUserMock(): User
    {
        $userMock = $this->createMock(User::class);

        if (!$userMock instanceof User) {
            $this->fail('Cannot create a user mock');
        }

        return $userMock;
    }

    /**
     * @return EntityManagerInterface|MockObject
     */
    private function createEntityManagerMock(): EntityManagerInterface
    {
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        if (!$entityManagerMock instanceof EntityManagerInterface) {
            $this->fail('Cannot create a entity manager mock');
        }

        return $entityManagerMock;
    }

    /**
     * @return UserPasswordEncoderInterface|MockObject
     */
    private function createUserPasswordEncoderMock(): UserPasswordEncoderInterface
    {
        $userPasswordEncoderMock = $this->createMock(UserPasswordEncoderInterface::class);

        if (!$userPasswordEncoderMock instanceof UserPasswordEncoderInterface) {
            $this->fail('Cannot create a user password encoder mock');
        }

        return $userPasswordEncoderMock;
    }
}
