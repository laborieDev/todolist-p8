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

class SecurityAppTest extends WebTestCase
{
    public function test()
    {
        $client = static::createClient();

        $router = $client->getContainer()->get('router');

        // NO CONNECTED 
        $client->request(
            Request::METHOD_GET, 
            $router->generate('homepage', [])
        );
        
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testLoginWithBadCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'john@doe.fr',
            '_password' => 'fakepassword'
        ]);
        $client->submit($form);

        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testLoginWithGoodCredentials()
    {
        $client = $this->login();

        $this->assertStringContainsString('Bienvenue', $client->getResponse()->getContent());
    }

    public function testLogout()
    {
        $client = $this->login();

        $router = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $router->generate('logout'));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertNull($client->getRequest()->getUser());
    }

    /******* FUNCTIONS ********/

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
