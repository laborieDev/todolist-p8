<?php

namespace App\Tests\Command;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CheckUsersTest extends KernelTestCase
{
    public function testExecuteWithAdmin()
    {
        $kernel = static::createKernel();

        /** @var ObjectManager $entityManager */
        $entityManager = static::bootKernel()->getContainer()->get('doctrine.orm.entity_manager');

        $application = new Application($kernel);
        $command = $application->find('todolist:user:check');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'username'  => 'admin',
            'password' => 'admin'
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $countUser = count($entityManager->getRepository(User::class)->findAll());

        $this->assertStringContainsString((string) $countUser, $output);
        $this->assertStringContainsString('Admin exists !', $output);
    }

    public function testExecuteWithoutAdmin()
    {
        $kernel = static::createKernel();

        /** @var ObjectManager $entityManager */
        $entityManager = static::bootKernel()->getContainer()->get('doctrine.orm.entity_manager');

        $adminUsers = $entityManager->getRepository(User::class)->findUsersByRole(User::USER_ADMIN_ROLE);

        foreach ($adminUsers as $admin) {
            $entityManager->remove($admin);
        }

        $entityManager->flush();

        $countUser = count($entityManager->getRepository(User::class)->findAll());

        $application = new Application($kernel);
        $command = $application->find('todolist:user:check');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'username'  => 'admin',
            'password' => 'admin'
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $this->assertStringContainsString((string) $countUser, $output);
        $this->assertStringContainsString('Admin is created !', $output);
    }
}
