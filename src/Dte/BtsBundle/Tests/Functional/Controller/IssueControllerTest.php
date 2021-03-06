<?php

namespace Dte\BtsBundle\Tests\Controller;

use Dte\BtsBundle\Tests\FixturesWebTestCase;

use Dte\BtsBundle\Entity\IssueTaskType;

use Symfony\Component\HttpFoundation\RedirectResponse;

class IssueControllerTest extends FixturesWebTestCase
{

    public function testProjectWithoutAuth()
    {
        $this->client->request('GET', '/issue');

        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);

        $this->assertRegExp('/\/login$/', $this->client->getResponse()->headers->get('location'));
    }

    public function testCompleteScenario()
    {
        $this->logInByUsername('admin');

        $crawler = $this->client->request('GET', '/issue/');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /issue/"
        );
        $crawler = $this->client->click($crawler->selectLink('Create a new issue')->link());

        $form = $crawler->selectButton('Create')->form(array(
            'dte_btsbundle_issue[project]'     => 1,
            'dte_btsbundle_issue[type]'        => IssueTaskType::TASK_TYPE,
            'dte_btsbundle_issue[summary]'     => 'Test issue summary',
            'dte_btsbundle_issue[description]' => 'Test issue summary descrption',
            'dte_btsbundle_issue[status]'      => 1,
            'dte_btsbundle_issue[priority]'    => 3,
            //'dte_btsbundle_issue[assignee]'    => 1,
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('#issue-code:contains("(BTS-8)")')->count(),
            'Missing element #issue-code:contains("(BTS-8)")'
        );

        $this->assertEquals('btn btn-default  active disabled ', $crawler->selectLink('Open')->attr('class'));

        $this->client->click($crawler->selectLink('In progress')->link());

        $crawler = $this->client->followRedirect();

        $this->assertEquals('btn btn-default ', $crawler->selectLink('Open')->attr('class'));
        $this->assertEquals('btn btn-default  active disabled ', $crawler->selectLink('In progress')->attr('class'));

        $crawler = $this->client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
           'dte_btsbundle_issue[summary]'  => 'Test issue summary updated',
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('[value="Test issue summary updated"]')->count(),
            'Missing element [value="Test issue summary updated"]'
        );
    }

    public function testAddSubtaskScenario()
    {
        $this->logInByUsername('admin');

        $crawler = $this->client->request('GET', '/issue/1');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /issue/"
        );
        $crawler = $this->client->click($crawler->selectLink('Add Subtask')->link());

        $form = $crawler->selectButton('Create')->form(array(
            'dte_btsbundle_issue[project]'     => 1,
            'dte_btsbundle_issue[type]'        => IssueTaskType::SUBTASK_TYPE,
            'dte_btsbundle_issue[summary]'     => 'Test issue summary',
            'dte_btsbundle_issue[description]' => 'Test issue summary descrption',
            'dte_btsbundle_issue[status]'      => 1,
            'dte_btsbundle_issue[priority]'    => 3,
            'dte_btsbundle_issue[assignee]'    => 1,
            'dte_btsbundle_issue[parent]'      => 1,
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('#issue-code:contains("(BTS-8)")')->count(),
            'Missing element #issue-code:contains("(BTS-8)")'
        );
    }
}
