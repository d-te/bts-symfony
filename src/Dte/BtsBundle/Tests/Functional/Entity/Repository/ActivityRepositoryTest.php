<?php

namespace Dte\BtsBundle\Tests\Functional\Entity\Repository;

use Dte\BtsBundle\Tests\FixturesWebTestCase;

class ActivityRepositoryTest extends FixturesWebTestCase
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

    public function testFindActivitiesByProject()
    {
        $project = $this->em
            ->getRepository('DteBtsBundle:Project')
            ->find(1);

        $activities = $this->em
            ->getRepository('DteBtsBundle:Activity')
            ->findActivitiesByProject($project);

        $this->assertCount(7, $activities);
    }

    public function testFindActivitiesByUser()
    {
        $user = $this->em
            ->getRepository('DteBtsBundle:User')
            ->find(2);

        $activities = $this->em
            ->getRepository('DteBtsBundle:Activity')
            ->findActivitiesByUser($user);

        $this->assertCount(9, $activities);
    }
}
