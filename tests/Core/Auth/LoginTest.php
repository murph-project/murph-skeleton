<?php

namespace App\Tests\Core\Auth;

use App\Repository\UserRepositoryQuery;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 * @coversNothing
 */
class LoginTest extends WebTestCase
{
    protected UserRepositoryQuery $query;
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->query = self::$container->get(UserRepositoryQuery::class);
    }

    public function testLoginRedirect(): void
    {
        $crawler = $this->client->request('GET', '/admin');

        $this->assertResponseStatusCodeSame(302);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testLoginUser(): void
    {
        $user = $this->query->create()->andWhere('.email=\'admin@localhost\'')->findOne();
        $this->client->loginUser($user);
        $this->client->request('GET', '/admin/account/');
        $this->assertResponseStatusCodeSame(200);
    }
}
