<?php

namespace Dte\BtsBundle\Tests\Functional\Entity\Repository;

use Dte\BtsBundle\Tests\FixturesWebTestCase;

use Symfony\Component\HttpFoundation\RedirectResponse;

class IssueRepositoryTest extends FixturesWebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->em = $this->client
            ->getKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testFindStoriesByProject()
    {
        $project = $this->em
            ->getRepository('DteBtsBundle:Project')
            ->find(1);

        $issues = $this->em
            ->getRepository('DteBtsBundle:Issue')
            ->findStoriesByProject($project);

        $this->assertCount(1, $issues);
    }

    public function testFindOpenedIssuesAssignedToUser()
    {
        $user = $this->em
            ->getRepository('DteBtsBundle:User')
            ->find(3);

        $issues = $this->em
            ->getRepository('DteBtsBundle:Issue')
            ->findOpenedIssuesAssignedToUser($user);

        $this->assertCount(5, $issues);
    }

    public function testFindOpenedIssuesByCollaborator()
    {
        $user = $this->em
            ->getRepository('DteBtsBundle:User')
            ->find(1);

        $issues = $this->em
            ->getRepository('DteBtsBundle:Issue')
            ->findOpenedIssuesByCollaborator($user);

        $this->assertCount(1, $issues);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
