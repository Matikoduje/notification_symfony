<?php

namespace App\Tests\Unit\Controller;

use App\Tests\Traits\RestoreExceptionHandlerTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    use RestoreExceptionHandlerTrait;

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->restoreExceptionHandler();
    }

    public function testIndexPageLoadsSuccessfully(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', 'Welcome');
    }
}
