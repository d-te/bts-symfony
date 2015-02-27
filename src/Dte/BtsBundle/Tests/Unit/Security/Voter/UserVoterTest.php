<?php

namespace Dte\BtsBundle\Tests\Unit\Security\Voter;

use Dte\BtsBundle\Entity\Role;
use Dte\BtsBundle\Entity\User;
use Dte\BtsBundle\Security\Voter\UserVoter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Security\Core\Role\RoleHierarchy
     */
    private $roleHierarchy;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken
     */
    private $token;

    public function setUp()
    {
        $this->roleHierarchy = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Role\RoleHierarchy')
            ->disableOriginalConstructor()
            ->setMethods(array('getReachableRoles'))
            ->getMock();
        $this->token = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')
            ->disableOriginalConstructor()
            ->setMethods(array('getUser', 'getRoles'))
            ->getMock();
    }

    public function tearDown()
    {
        $this->roleHierarchy = null;
        $this->token         = null;
    }

    /**
     * @dataProvider supportsAttributeDataProvider
     *
     * @param string $attribute
     * @param boolean $expected
     */
    public function testSupportsAttribute($attribute, $expected)
    {
        $voter = new UserVoter($this->roleHierarchy);

        $this->assertEquals($expected, $voter->supportsAttribute($attribute));
    }

    /**
     * Dataprovider for  testSupportsAttribute
     *
     * @return array
     */
    public function supportsAttributeDataProvider()
    {
        return array(
            array('create', true),
            array('view', true),
            array('edit', true),
            array('profile', true),
            array('delete', false),
        );
    }

    public function testSupportsClass()
    {
        $voter = new UserVoter($this->roleHierarchy);

        $this->assertTrue($voter->supportsClass('Dte\BtsBundle\Entity\User'));
        $this->assertFalse($voter->supportsClass('Dte\BtsBundle\Entity\Issue'));
    }

    public function testVoteWithNotSupportedClass()
    {
        $voter = new UserVoter($this->roleHierarchy);

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $voter->vote($this->token, new \StdClass(), array('view')));
    }

    public function testVoteWithNotSupportedAttribute()
    {
        $voter = new UserVoter($this->roleHierarchy);

        $object = new User();

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $voter->vote($this->token, $object, array('notsupported')));
    }

    public function testVoteWithNotAuthorizedUser()
    {
        $voter = new UserVoter($this->roleHierarchy);

        $object = new User();

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($this->token, $object, array('view')));
    }

    public function testVoteWithTheRole()
    {
        $role = new Role();
        $role->setRole('ROLE_ADMIN');

        $this->token
                ->expects($this->atLeastOnce())
                ->method('getRoles')
                ->will($this->returnValue(array($role)));

        $this->token
                ->expects($this->atLeastOnce())
                ->method('getUser')
                ->will($this->returnValue(new User()));

        $this->roleHierarchy
                ->expects($this->atLeastOnce())
                ->method('getReachableRoles')
                ->will($this->returnValue(array($role)));

        $voter = new UserVoter($this->roleHierarchy);

        $object = new User();

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($this->token, $object, array('view')));
        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token, 'Dte\\BtsBundle\\Entity\\User', array('view'))
        );
    }

    /**
     * @dataProvider voteWithUserRoleDataProvider
     *
     * @param  string $currentUserEmail
     * @param  string $memberEmail
     * @param  string $attribute
     * @param  boolean $expected
     */
    public function testVoteWithUserRole($currentUserEmail, $memberEmail, $attribute, $expected)
    {
        $role = new Role();
        $role->setRole('ROLE_USER');

        $currentUser = new User();
        $currentUser->setEmail($currentUserEmail);

        $this->token
            ->expects($this->atLeastOnce())
            ->method('getRoles')
            ->will($this->returnValue(array($role)));

        $this->token
            ->expects($this->atLeastOnce())
            ->method('getUser')
            ->will($this->returnValue($currentUser));

        $this->roleHierarchy
            ->expects($this->atLeastOnce())
            ->method('getReachableRoles')
            ->will($this->returnValue(array($role)));

        $voter = new UserVoter($this->roleHierarchy);

        $object = new User();
        $object->setEmail($memberEmail);

        $this->assertEquals($expected, $voter->vote($this->token, $object, array($attribute)));
    }

    /**
     * Dataprovider for testVoteWithUserRole
     * @return  array
     */
    public function voteWithUserRoleDataProvider()
    {
        return array(
            array('e1@email', 'e11@email', 'view', VoterInterface::ACCESS_GRANTED),
            array('e1@email', 'e11@email', 'edit', VoterInterface::ACCESS_DENIED),
            array('e1@email', 'e11@email', 'create', VoterInterface::ACCESS_DENIED),
            array('e1@email', 'e11@email', 'profile', VoterInterface::ACCESS_DENIED),
            array('e11@email', 'e11@email', 'view', VoterInterface::ACCESS_GRANTED),
            array('e11@email', 'e11@email', 'edit', VoterInterface::ACCESS_GRANTED),
            array('e11@email', 'e11@email', 'profile', VoterInterface::ACCESS_GRANTED),
            array('e11@email', 'e11@email', 'create', VoterInterface::ACCESS_DENIED),
        );
    }
}
