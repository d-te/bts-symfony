<?php

namespace Dte\BtsBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultControllerTest extends WebTestCase
{

    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testIndexWithoutAuth()
    {
        $this->client->request('GET', '/');

        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);

        $this->assertRegExp('/\/login$/', $this->client->getResponse()->headers->get('location'));
    }

    public function testIndexWithAuth()
    {
        $this->logIn();

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

    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewall = 'secured_area';
        $token = new UsernamePasswordToken('admin', null, $firewall, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
