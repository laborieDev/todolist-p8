<?php

namespace Tests\AppBundle\Controller;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserEntityTest extends KernelTestCase
{

    public function testIndex()
    {
        $kernel = self::bootKernel();

        $entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        
        $user = new User();
        $username = 'Test Username '.uniqid();
        $email = 'email@test'.uniqid().'.com';
        $user->setUsername($username);
        $user->setEmail($email);

        $password = $kernel->getContainer()->get('security.password_encoder')->encodePassword($user, 'passwordTest');
        $user->setPassword($password);

        $this->assertEquals($user->getUsername(), $username);
        $this->assertEquals($user->getEmail(), $email);
        $this->assertEquals($user->getPassword(), $password);

        $entityManager->persist($user);
        $entityManager->flush();

        $id = $user->getId();
        $this->assertEquals(
            $entityManager->getRepository(User::class)->find($id),
            $user
        );
        
        $task = $entityManager->getRepository(Task::class)->findOneBy([]);
        
        $user->addTask($task);
        $this->assertEquals($user->getTasks()->toArray(), [$task]);

        $user->removeTask($task);
        $this->assertEquals($user->getTasks()->toArray(), []);

        $user->setRoles([User::USER_ADMIN_ROLE]);
        $this->assertEquals($user->getRoles(), [User::USER_ADMIN_ROLE, User::USER_USER_ROLE]);


        $this->assertEquals($user->eraseCredentials(), null);
    }
}
