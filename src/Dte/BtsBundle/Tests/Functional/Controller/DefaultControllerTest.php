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

    public function testLoginForm()
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertTrue($crawler->filter('h1:contains("Login")')->count() > 0);

        $this->assertTrue($crawler->filter('input[name="_username"]')->count() > 0);

        $this->assertTrue($crawler->filter('input[name="_password"]')->count() > 0);

        $this->assertTrue($crawler->filter('button[type=submit]')->count() > 0);
    }
}
