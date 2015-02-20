<?php

namespace Dte\BtsBundle\Tests\Functional\Controller;

use Dte\BtsBundle\Tests\FixturesWebTestCase;

use Symfony\Component\HttpFoundation\RedirectResponse;

class ProjectControllerTest extends FixturesWebTestCase
{
    /**
     * @dataProvider pagesDataProvider
     */
    public function testProjectWithoutAuth($method, $url)
    {
        $this->client->request($method, $url);

        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);

        $this->assertRegExp('/\/login$/', $this->client->getResponse()->headers->get('location'));
    }

    public function pagesDataProvider()
    {
        return array(
            array('GET', '/project'),
            array('GET', '/project/1/members'),
            array('GET', '/project/1/stories'),
        );
    }

    public function testCompleteScenario()
    {
        $this->logInByUsername('admin');

        $crawler = $this->client->request('GET', '/project/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /project/");
        $crawler = $this->client->click($crawler->selectLink('Create a new project')->link());

        $form = $crawler->selectButton('Create')->form(array(
            'dte_btsbundle_project[code]'  => 'CODE',
            'dte_btsbundle_project[label]'  => '1111Test project',
            'dte_btsbundle_project[summary]'  => 'Some summary',
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('#entity-header strong[class="project-code"]')->count(), 'Missing element strong:contains("(CODE)&nbsp;)")');

        $crawler = $this->client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
           'dte_btsbundle_project[label]'  => '1111Test project updated',
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('[value="1111Test project updated"]')->count(), 'Missing element [value="1111Test project updated"]');

        $this->client->submit($crawler->selectButton('Delete')->form());
        $crawler = $this->client->followRedirect();

        $this->assertNotRegExp('/1111Test project/', $this->client->getResponse()->getContent());
    }

    public function testProjecMemberScenario()
    {
        $this->logInByUsername('admin');

        $crawler = $this->client->request('GET', '/project/1/members');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /project/");
        $this->assertEquals('[{"id":1,"label":"Admin A.A."},{"id":2,"label":"Manager M.M."},{"id":3,"label":"Operator the first O.O."}]', $this->client->getResponse()->getContent());
    }

    public function testProjecStoriesScenario()
    {
        $this->logInByUsername('admin');

        $crawler = $this->client->request('GET', '/project/1/stories');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /project/");
        $this->assertEquals('[{"id":1,"label":"( BTS-1 ) Add manager of systems guides"}]', $this->client->getResponse()->getContent());
    }
}
