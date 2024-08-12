<?php

namespace App\Tests\Integration\Controller;

use App\Entity\User;
use App\Tests\Fixtures\UserFixture;
use App\Tests\Traits\RestoreExceptionHandlerTrait;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationControllerTest extends WebTestCase
{
    use RestoreExceptionHandlerTrait;
    private ?EntityManagerInterface $entityManager;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $passwordHasher = $this->client->getContainer()->get('security.user_password_hasher');
        if (!$passwordHasher instanceof UserPasswordHasherInterface) {
            throw new \RuntimeException('Password hasher not available');
        }
        $loader = new Loader();
        $loader->addFixture(new UserFixture($passwordHasher));

        $purger = new ORMPurger($this->entityManager);
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testSuccessfulRegister(): void
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('Register')->form([
            'registration_form[email]' => 'unique@example.com',
            'registration_form[plainPassword]' => 'password',
            'registration_form[agreeTerms]' => true,
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/');

        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneByEmail('unique@example.com');
        $this->assertNotNull($user);
        $this->assertTrue(password_verify('password', $user->getPassword()));

        $this->assertEmailCount(1);
    }

    public function testRegisterWithExistingEmail(): void
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('Register')->form([
            'registration_form[email]' => 'existing@example.com',
            'registration_form[plainPassword]' => 'password',
            'registration_form[agreeTerms]' => true,
        ]);

        $this->client->submit($form);
        $this->assertSelectorTextContains('.form-group', 'There is already an account with this email');

        $userCount = $this->entityManager->getRepository(User::class)->count(['email' => 'existing@example.com']);
        $this->assertEquals(1, $userCount);
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
