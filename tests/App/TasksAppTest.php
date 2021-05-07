<?php

namespace Tests\App;

use App\Entity\Task;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;

class TasksAppTest extends WebTestCase
{
    public function testCreateTask()
    {
        $client = $this->login();

        $router = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $router->generate('task_create', []));
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Task Test Title',
            'task[content]' => 'Task Test Content'
        ]);
        $client->submit($form);

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testEditTask()
    {
        $client = $this->login();

        $router = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $oneTask = $entityManager->getRepository(Task::class)->findOneBy([]);

        $crawler = $client->request('GET', $router->generate('task_edit', ['id' => $oneTask->getId()]));
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Task Edit Test',
            'task[content]' => 'Task Edit Test Content'
        ]);
        $client->submit($form);

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testEditTaskBadUser()
    {
        $client = $this->login(false);

        $router = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $oneTask = $entityManager->getRepository(Task::class)->findOneBy([]);

        $client->request('GET', $router->generate('task_edit', ['id' => $oneTask->getId()]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteTask()
    {
        $client = $this->login();

        $router = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $oneTask = $entityManager->getRepository(Task::class)->findOneBy([]);

        $crawler = $client->request('GET', $router->generate('task_delete', ['id' => $oneTask->getId()]));

        $client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testToogleTask()
    {
        $client = $this->login();

        $router = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $oneTask = $entityManager->getRepository(Task::class)->findOneBy([
            'isDone' => false
        ]);

        $crawler = $client->request('GET', $router->generate('task_toggle', ['id' => $oneTask->getId()]));

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testListDoneTask()
    {
        $client = $this->login();

        $router = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $router->generate('task_list', ['isDone' => 1]));

        $this->assertSelectorExists('.glyphicon.glyphicon-ok');
    }

    public function testListNoDoneTask()
    {
        $client = $this->login();

        $router = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $router->generate('task_list', ['isDone' => 0]));

        $this->assertSelectorNotExists('.glyphicon.glyphicon-ok');
    }

   /****************** FUNCTIONS *******************/

   public function login($isAdmin = true)
   {
       $userDatas = [
           'username' => 'admin',
           'password' => 'admin'
       ];

       if (!$isAdmin) {
           $userDatas = [
               'username' => 'User Two',
               'password' => 'user'
           ];
       }

       $client = static::createClient();
       $crawler = $client->request('GET', '/login');
       $form = $crawler->selectButton('Se connecter')->form([
           '_username' => $userDatas['username'],
           '_password' => $userDatas['password']
       ]);
       $client->submit($form);

       $client->followRedirect();

       return $client;
   }
}
