<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function testGetAndSetEmail(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $this->assertSame('test@example.com', $user->getEmail());
    }

    public function testGetAndSetRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        $roles = $user->getRoles();

        $this->assertCount(2, $roles);
        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles);
    }

    public function testGetAndSetPassword(): void
    {
        $user = new User();
        $user->setPassword('hashed_password');

        $this->assertSame('hashed_password', $user->getPassword());
    }

    public function testUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('identifier@example.com');

        $this->assertSame('identifier@example.com', $user->getUserIdentifier());
    }

}
