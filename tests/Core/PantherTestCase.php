<?php

namespace App\Tests\Core;

use App\Repository\UserRepositoryQuery;
use Symfony\Component\Panther\Client as PantherClient;
use Symfony\Component\Panther\PantherTestCase as BasePantherTestCase;

abstract class PantherTestCase extends BasePantherTestCase
{
    protected UserRepositoryQuery $query;
    protected PantherClient $client;

    protected function container()
    {
        if (null === self::$container) {
            static::bootKernel();
        }

        return self::$container;
    }

    protected function setUp(): void
    {
        $this->client = static::createPantherClient([
            'external_base_uri' => 'http://localhost:9080'
        ]);
        $this->query = $this->container()->get(UserRepositoryQuery::class);
    }

    protected function authenticateAdmin(): void
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Login', [
            '_username' => 'admin@localhost',
            '_password' => 'admin_password',
        ]);
        $this->client->waitFor('.nav-item-label');
    }

    protected function authenticateWriter(): void
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Login', [
            '_username' => 'writer@localhost',
            '_password' => 'writer_password',
        ]);
        $this->client->waitFor('.nav-item-label');
    }
}
