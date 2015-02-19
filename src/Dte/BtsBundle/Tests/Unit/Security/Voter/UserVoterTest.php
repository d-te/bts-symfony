<?php

namespace Dte\BtsBundle\Tests\Unit\Security\Voter;

use Dte\BtsBundle\Entity\Role;
use Dte\BtsBundle\Entity\User;
use Dte\BtsBundle\Security\Voter\UserVoter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserVoterTest extends \PHPUnit_Framework_TestCase
{

    private $roleHierarchy;

    private $token;

    public function setUp()
    {
        $this->roleHierarchy = $this->getMockBuilder('Symfony\Component\Security\Core\Role\RoleHierarchy')
                        ->disableOriginalConstructor()
                        ->setMethods(array('getReachableRoles'))
                        ->getMock();
        $this->token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')
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
     */
    public function testSupportsAttribute($attribute, $expected)
    {
        $voter = new UserVoter($this->roleHierarchy);

        $this->assertEquals($expected, $voter->supportsAttribute($attribute));
    }

    public function supportsAttributeDataProvider()
    {
        return array(
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

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $voter->vote($this->token, new \StdClass(), array()));
    }

    public function testVoteWithNotSupportedAttribute()
    {
        $voter = new UserVoter($this->roleHierarchy);

        $object = new User();

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $voter->vote($this->token, $object, array('not_supported')));
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
                ->expects($this->once())
                ->method('getRoles')
                ->will($this->returnValue(array($role)));

        $this->token
                ->expects($this->once())
                ->method('getUser')
                ->will($this->returnValue(new User()));

        $this->roleHierarchy
                ->expects($this->once())
                ->method('getReachableRoles')
                ->will($this->returnValue(array($role)));

        $voter = new UserVoter($this->roleHierarchy);

        $object = new User();

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($this->token, $object, array('view')));
    }

    /**
     * @dataProvider voteWithUserRoleDataProvider
     */
    public function testVoteWithUserRole($currentUserId, $objectId, $attribute, $expected)
    {
        $role = new Role();
        $role->setRole('ROLE_USER');

        $currentUser = $this->getMockBuilder('Dte\BtsBundle\Entity\User')
                        ->setMethods(array('getId'))
                        ->getMock();
        $currentUser->expects($this->any())->method('getId')->will($this->returnValue($currentUserId));

        $this->token
                ->expects($this->any())
                ->method('getRoles')
                ->will($this->returnValue(array($role)));

        $this->token
                ->expects($this->any())
                ->method('getUser')
                ->will($this->returnValue($currentUser));

        $this->roleHierarchy
                ->expects($this->any())
                ->method('getReachableRoles')
                ->will($this->returnValue(array($role)));

        $voter = new UserVoter($this->roleHierarchy);

        $object = $this->getMockBuilder('Dte\BtsBundle\Entity\User')
                        ->setMethods(array('getId'))
                        ->getMock();
        $object->expects($this->any())->method('getId')->will($this->returnValue($objectId));

        $this->assertEquals($expected, $voter->vote($this->token, $object, array($attribute)));
    }

    public function voteWithUserRoleDataProvider()
    {
        return array(
            array(1, 11, 'view', VoterInterface::ACCESS_GRANTED),
            array(1, 11, 'edit', VoterInterface::ACCESS_DENIED),
            array(1, 11, 'profile', VoterInterface::ACCESS_DENIED),
            array(11, 11, 'view', VoterInterface::ACCESS_GRANTED),
            array(11, 11, 'edit', VoterInterface::ACCESS_GRANTED),
            array(11, 11, 'profile', VoterInterface::ACCESS_GRANTED),
        );
    }
}
