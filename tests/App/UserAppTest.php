<?php

namespace Tests\App;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserAppTest extends WebTestCase
{
    public function testUsersList()
    {
        $client = $this->login();

        $router = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $router->generate('user_list', []));

        $this->assertStringContainsString('Liste des utilisateurs', $client->getResponse()->getContent());
    }

    public function testCreateUser()
    {
        $client = $this->login();

        $router = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $router->generate('user_create', []));

        $this->assertStringContainsString('Créer un utilisateur', $client->getResponse()->getContent());

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'test'.uniqid().'_username',
            'user[password][first]' => 'test_password',
            'user[password][second]' => 'test_password',
            'user[email]' => 'test'.uniqid().'@test.fr',
        ]);
        $client->submit($form);

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testEditUser()
    {
        $client = $this->login();

        $router = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $users = $entityManager->getRepository(User::class)->findUsersByForbiddenRole(User::USER_ADMIN_ROLE);

        if(!is_array($users)) return false;

        $userID = $users[0]->getId();
        $crawler = $client->request('GET', $router->generate('user_edit', ['id' => $userID]));

        $this->assertStringContainsString('Modifier', $client->getResponse()->getContent());

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'test_edit_username',
            'user[password][first]' => 'test_password',
            'user[password][second]' => 'test_password',
            'user[email]' => 'test'.uniqid().'@test.fr',
        ]);
        $client->submit($form);

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
    }

    public function login()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'admin',
            '_password' => 'admin'
        ]);
        $client->submit($form);

        $client->followRedirect();

        return $client;
    }
}
