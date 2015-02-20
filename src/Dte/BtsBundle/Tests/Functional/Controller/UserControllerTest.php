<?php

namespace Dte\BtsBundle\Tests\Functional\Controller;

use Dte\BtsBundle\Tests\FixturesWebTestCase;

use Symfony\Component\HttpFoundation\RedirectResponse;

class UserControllerTest extends FixturesWebTestCase
{

    public function testProfileWithoutAuth()
    {
        $this->client->request('GET', '/user/profile');

        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);

        $this->assertRegExp('/\/login$/', $this->client->getResponse()->headers->get('location'));
    }

    public function testCompleteScenario()
    {
        $this->logInByUsername('admin');

        $crawler = $this->client->request('GET', '/user/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /user/");
        $crawler = $this->client->click($crawler->selectLink('Create a new user')->link());

        $form = $crawler->selectButton('Create')->form(array(
            'dte_btsbundle_user[email]'    => 'test@bts.dev',
            'dte_btsbundle_user[username]' => 'tester',
            'dte_btsbundle_user[fullname]' => 'Tester T.T.',
            'dte_btsbundle_user[password]' => 'tester',
            'dte_btsbundle_user[avatar]'   => 'https://avatars3.githubusercontent.com/u/3748005?v=3&s=460',
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('#user-credentials:contains("Tester T.T. ( test@bts.dev )")')->count(), 'Missing element #user-credentials:contains("Tester T.T. ( test@bts.dev )")');

        $crawler = $this->client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
           'dte_btsbundle_user[fullname]'  => 'Tester T.T.updated',
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('[value="Tester T.T.updated"]')->count(), 'Missing element [value="1111Test user updated"]');
    }

    public function testProfileScenario()
    {
        $this->logInByUsername('admin');

        $crawler = $this->client->request('GET', '/user/profile');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /user/profile/");

        $this->assertGreaterThan(0, $crawler->filter('#user-credentials:contains("Admin A.A. ( admin@bts.dev )")')->count(), 'Missing element #user-credentials:contains("Admin A.A. ( admin@bts.dev )")');

        $crawler = $this->client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
           'dte_btsbundle_user[fullname]'  => 'Tester T.T.updated',
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('[value="Tester T.T.updated"]')->count(), 'Missing element [value="1111Test user updated"]');
    }
}
