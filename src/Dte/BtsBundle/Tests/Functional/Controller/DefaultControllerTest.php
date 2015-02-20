<?php

namespace Dte\BtsBundle\Tests\Functional\Controller;

use Dte\BtsBundle\Tests\FixturesWebTestCase;

use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultControllerTest extends FixturesWebTestCase
{

    public function testIndexWithoutAuth()
    {
        $this->client->request('GET', '/');

        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);

        $this->assertRegExp('/\/login$/', $this->client->getResponse()->headers->get('location'));
    }

    public function testIndexWithAuth()
    {
        $this->logInByUsername('admin');

        $this->client->request('GET', '/');

        $this->assertFalse($this->client->getResponse() instanceof RedirectResponse);
    }

    public function testLoginLogoutScenario()
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertTrue($crawler->filter('h1:contains("Login")')->count() > 0);

        $this->assertTrue($crawler->filter('input[name="_username"]')->count() > 0);

        $this->assertTrue($crawler->filter('input[name="_password"]')->count() > 0);

        $this->assertTrue($crawler->filter('button[type=submit]')->count() > 0);

        $form = $crawler->selectButton('Login')->form(array(
            '_username'  => 'admin',
            '_password'  => 'admin',
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->selectLink('Admin A.A.')->count());

        $crawler = $this->client->click($crawler->selectLink('Log Out')->link());

        $crawler = $this->client->followRedirect();

        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);

        $this->assertRegExp('/\/login$/', $this->client->getResponse()->headers->get('location'));
    }
}
