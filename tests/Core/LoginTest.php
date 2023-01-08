<?php

namespace App\Tests\Core;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testLoginRedirect(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }
}
