<?php

namespace Dte\BtsBundle\Tests\Functional\Entity\Repository;

use Dte\BtsBundle\Entity\User;
use Dte\BtsBundle\Tests\FixturesWebTestCase;

use Symfony\Component\Security\Core\User\UserInterface;

class UserRepositoryTest extends FixturesWebTestCase
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

    public function testLoadUserByUsername()
    {
        $user = $this->em
            ->getRepository('DteBtsBundle:User')
            ->loadUserByUsername('admin');

        $this->assertTrue($user instanceof UserInterface);
        $this->assertTrue($user instanceof User);
        $this->assertEquals(1, $user->getId());
    }

    /**
     * @expectedException        Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     * @expectedExceptionMessage Unable to find an active admin DteBtsBundle:User object identified by "admin111".
     */
    public function testLoadUserByUsernameWithWrongUsername()
    {
        $user = $this->em
            ->getRepository('DteBtsBundle:User')
            ->loadUserByUsername('admin111');
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
