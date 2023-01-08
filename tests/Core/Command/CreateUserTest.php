<?php

namespace App\Tests\Core\Command;

use App\Repository\UserRepositoryQuery;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 * @coversNothing
 */
class CreateUserTest extends KernelTestCase
{
    protected UserRepositoryQuery $query;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->query = self::$container->get(UserRepositoryQuery::class);
    }

    public function testCommandExecute(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('murph:user:create');
        $commandTester = new CommandTester($command);

        $commandTester->setInputs([
            'admin@localhost',
            'admin_password',
            'y',
            'n',
        ]);
        $commandTester->execute(['command' => $command->getName()]);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('User created!', $output);

        $commandTester->setInputs([
            'writer@localhost',
            'writer_password',
            'n',
            'y',
        ]);
        $commandTester->execute(['command' => $command->getName()]);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('User created!', $output);
    }

    public function testCreatedUsers(): void
    {
        $users = $this->query->create()->find();
        $this->assertEquals(2, count($users));

        $this->assertEquals('admin@localhost', $users[0]->getEmail());
        $this->assertEquals('admin@localhost', $users[0]->getUsername());
        $this->assertEquals('writer@localhost', $users[1]->getEmail());
        $this->assertEquals('writer@localhost', $users[1]->getUsername());

        $this->assertEquals(true, $users[0]->getIsAdmin());
        $this->assertEquals(false, $users[0]->getIsWriter());
        $this->assertEquals(false, $users[1]->getIsAdmin());
        $this->assertEquals(true, $users[1]->getIsWriter());
    }
}
