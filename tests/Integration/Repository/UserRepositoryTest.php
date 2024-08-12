<?php

namespace App\Tests\Integration\Repository;

use App\Entity\User;
use App\Tests\Traits\RestoreExceptionHandlerTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    use RestoreExceptionHandlerTrait;

    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSaveAndRetrieveUser(): void
    {

        $user = new User();
        $user->setEmail('test@example.com')
            ->setPassword('hashed_password')
            ->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $retrievedUser = $this->entityManager->getRepository(User::class)->find($user->getId());

        $this->assertNotNull($retrievedUser);
        $this->assertSame('test@example.com', $retrievedUser->getEmail());
        $this->assertSame('hashed_password', $retrievedUser->getPassword());
        $this->assertContains('ROLE_USER', $retrievedUser->getRoles());
    }

    public function testUpdateUser(): void
    {

        $user = new User();
        $user->setEmail('update@example.com')
            ->setPassword('hashed_password')
            ->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $user->setEmail('updated@example.com');
        $this->entityManager->flush();

        $updatedUser = $this->entityManager->getRepository(User::class)->find($user->getId());

        $this->assertNotNull($updatedUser);
        $this->assertSame('updated@example.com', $updatedUser->getEmail());
    }

    public function testDeleteUser(): void
    {
        $user = new User();
        $user->setEmail('delete@example.com')
            ->setPassword('hashed_password')
            ->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userToDeleteId = $user->getId();

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $deletedUser = $this->entityManager->getRepository(User::class)->find($userToDeleteId);

        $this->assertNull($deletedUser);
    }

    protected function tearDown(): void
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();

        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;

        $this->restoreExceptionHandler();
    }
}
