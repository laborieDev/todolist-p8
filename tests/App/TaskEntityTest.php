<?php

namespace Tests\AppBundle\Controller;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskEntityTest extends KernelTestCase
{

    public function testIndex()
    {
        $kernel = self::bootKernel();

        $entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        
        $task = new Task();
        $createdAt = new \DateTime();
        $task->setCreatedAt($createdAt);
        $task->setTitle('Test Title');
        $task->setContent('Test Content');
        $task->toggle(true);
        
        $user = $entityManager->getRepository(User::class)->findOneBy([]);
        $task->setUser($user);

        $this->assertEquals($task->getCreatedAt(), $createdAt);
        $this->assertEquals($task->getTitle(), 'Test Title');
        $this->assertEquals($task->getContent(), 'Test Content');
        $this->assertEquals($task->isDone(), true);
        $this->assertEquals($task->getUser(), $user);

        $task->setIsDone(false);
        $this->assertEquals($task->getIsDone(), false);

        $entityManager->persist($task);
        $entityManager->flush();

        $id = $task->getId();

        $this->assertEquals(
            $entityManager->getRepository(Task::class)->find($id),
            $task
        );
    }
}
