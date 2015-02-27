<?php

namespace Dte\BtsBundle\Tests\Unit\Security\Voter;

use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\Project;
use Dte\BtsBundle\Entity\Role;
use Dte\BtsBundle\Entity\User;
use Dte\BtsBundle\Security\Voter\IssueVoter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class IssueVoterTest extends \PHPUnit_Framework_TestCase
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
        $voter = new IssueVoter($this->roleHierarchy);

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
            array('delete', false),
            array('profile', false),
        );
    }

    public function testSupportsClass()
    {
        $voter = new IssueVoter($this->roleHierarchy);

        $this->assertTrue($voter->supportsClass('Dte\BtsBundle\Entity\Issue'));
        $this->assertFalse($voter->supportsClass('Dte\BtsBundle\Entity\Project'));
    }

    public function testVoteWithNotSupportedClass()
    {
        $voter = new IssueVoter($this->roleHierarchy);

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $voter->vote($this->token, new \StdClass(), array('view')));
    }

    public function testVoteWithNotSupportedAttribute()
    {
        $voter = new IssueVoter($this->roleHierarchy);

        $object = new Issue();

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $voter->vote($this->token, $object, array('notsupported')));
    }

    public function testVoteWithNotAuthorizedUser()
    {
        $voter = new IssueVoter($this->roleHierarchy);

        $object = new Issue();

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($this->token, $object, array('view')));
    }

    public function testVoteWithTheRole()
    {
        $role = new Role();
        $role->setRole('ROLE_MANAGER');

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

        $voter = new IssueVoter($this->roleHierarchy);

        $object = new Issue();

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($this->token, $object, array('view')));
        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token, 'Dte\\BtsBundle\\Entity\\Issue', array('view'))
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
        $role->setRole('ROLE_OPERATOR');

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

        $voter = new IssueVoter($this->roleHierarchy);

        $user = new User();
        $user->setEmail($memberEmail);

        $object = new Issue();
        $project = new Project();
        $project->addMember($user);
        $object->setProject($project);

        $this->assertEquals($expected, $voter->vote($this->token, $object, array($attribute)));
    }

    /**
     * Dataprovider for testVoteWithUserRole
     * @return  array
     */
    public function voteWithUserRoleDataProvider()
    {
        return array(
            array('e1@email', 'e11@email', 'view', VoterInterface::ACCESS_DENIED),
            array('e1@email', 'e11@email', 'edit', VoterInterface::ACCESS_DENIED),
            array('e1@email', 'e11@email', 'create', VoterInterface::ACCESS_GRANTED),
            array('e11@email', 'e11@email', 'view', VoterInterface::ACCESS_GRANTED),
            array('e11@email', 'e11@email', 'edit', VoterInterface::ACCESS_GRANTED),
            array('e11@email', 'e11@email', 'create', VoterInterface::ACCESS_GRANTED),
        );
    }
}
