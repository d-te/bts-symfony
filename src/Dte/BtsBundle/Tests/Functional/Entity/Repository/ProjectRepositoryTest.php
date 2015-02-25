<?php

namespace Dte\BtsBundle\Tests\Functional\Entity\Repository;

use Dte\BtsBundle\Tests\FixturesWebTestCase;

class ProjectRepositoryTest extends FixturesWebTestCase
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

    public function testFindByMember()
    {
        $member = $this->em
            ->getRepository('DteBtsBundle:User')
            ->find(1);

        $projects = $this->em
            ->getRepository('DteBtsBundle:Project')
            ->findByMember($member);

        $this->assertCount(2, $projects);
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
