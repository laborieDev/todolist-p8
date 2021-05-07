<?php

namespace App\Tests\Command;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class AnonymousTaskTest extends KernelTestCase
{
    public function testWithoutAnonymousTask()
    {
        $kernel = static::createKernel();

        /** @var ObjectManager $entityManager */
        $entityManager = static::bootKernel()->getContainer()->get('doctrine.orm.entity_manager');

        $application = new Application($kernel);
        $command = $application->find('todolist:task:check-anonymous');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $countAnonymousTask = count($entityManager->getRepository(Task::class)->findBy([
            'user' => null
        ]));

        $this->assertStringContainsString((string) $countAnonymousTask, $output);
    }

    public function testWithoutAnonymousUser()
    {
        $kernel = static::createKernel();

        /** @var ObjectManager $entityManager */
        $entityManager = static::bootKernel()->getContainer()->get('doctrine.orm.entity_manager');

        $anonymousUser = $entityManager->getRepository(User::class)->findOneBy([
            "username" => User::ANONYMOUS_USERNAME,
            "email" => User::ANONYMOUS_EMAIL
        ]);

        if ($anonymousUser != null) {
            $entityManager->remove($anonymousUser);
            $entityManager->flush();
        }

        $application = new Application($kernel);
        $command = $application->find('todolist:task:check-anonymous');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $anonymousUser = $entityManager->getRepository(User::class)->findOneBy([
            "username" => User::ANONYMOUS_USERNAME,
            "email" => User::ANONYMOUS_EMAIL
        ]);

        $this->assertNotNull($anonymousUser);
    }
}
