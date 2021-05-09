<?php

namespace Tests\AppBundle\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserFormTest extends WebTestCase
{
    public function test()
    {
        $client = static::createClient();

        $router = $client->getContainer()->get('router');
//        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

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
}
