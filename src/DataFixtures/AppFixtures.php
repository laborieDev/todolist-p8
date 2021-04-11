<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\Client;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
 
    public function __construct(UserPasswordEncoderInterface $encoder) 
    {
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $entityManager)
    {
        //CREATE ADMIN
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@todolist.fr');
        $admin->setRoles([User::USER_ADMIN_ROLE]);
        $passwordEncoded = $this->encoder->encodePassword($admin, 'admin');
        $admin->setPassword($passwordEncoded);
        $entityManager->persist($admin);

        //CREATE ANOTHER USER
        $user = new User();
        $user->setUsername('User One');
        $user->setEmail('user@todolist.fr');
        $passwordEncoded = $this->encoder->encodePassword($user, 'user');
        $user->setPassword($passwordEncoded);
        $entityManager->persist($user);

        //CREATE TASKS
        for ($i = 0; $i < 15; $i++) {
            $task = new Task;
            $task->setTitle("Task ".$i);
            $content = "Contenu de la tâche ".$i." Voici les choses à faire !";
            $task->setContent($content);

            $taskUser = $user;
            if ($i%2 == 0) {
                $taskUser = $admin;
            }

            $isDone = false;
            if ($i%3 == 0) {
                $isDone = true;
            }

            $task->setUser($taskUser);
            $task->setIsDone($isDone);

            $entityManager->persist($task);
        }

        $entityManager->flush();
    }
}
