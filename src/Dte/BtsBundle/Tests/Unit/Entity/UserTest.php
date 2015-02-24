<?php

namespace Dte\BtsBundle\Tests\Unit\Entity;

use Dte\BtsBundle\Entity\User;
use Dte\BtsBundle\Entity\Role;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserTest extends \PHPUnit_Framework_TestCase
{

    public function testImplementsUserInterface()
    {
        $user = new User();

        $this->assertTrue($user instanceof UserInterface);
    }

    public function testImplementsEquatableInterface()
    {
        $user = new User();

        $this->assertTrue($user instanceof EquatableInterface);
    }

    public function testEmailSetterGetter()
    {
        $user = new User();

        $this->assertNull($user->getEmail());

        $user->setEmail('test@email.dev');

        $this->assertEquals('test@email.dev', $user->getEmail());
    }

    public function testUsernameSetterGetter()
    {
        $user = new User();

        $this->assertNull($user->getUsername());

        $user->setUsername('name');

        $this->assertEquals('name', $user->getUsername());
    }

    public function testPasswordSetterGetter()
    {
        $user = new User();

        $this->assertNull($user->getPassword());

        $user->setPassword('password');

        $this->assertEquals('password', $user->getPassword());
    }

    public function testFullnameSetterGetter()
    {
        $user = new User();

        $this->assertNull($user->getFullname());

        $user->setFullname('fullname');

        $this->assertEquals('fullname', $user->getFullname());
    }

    public function testAvatarSetterGetter()
    {
        $user = new User();

        $this->assertNull($user->getAvatar());

        $user->setAvatar('Avatar');

        $this->assertEquals('Avatar', $user->getAvatar());
    }

    public function testRolesGetter()
    {
        $user = new User();

        $this->assertEquals(array(), $user->getRoles());
    }

    public function testProjectsGetter()
    {
        $user = new User();

        $this->assertEquals(array(), $user->getProjects());
    }

    public function testActivitiesGetter()
    {
        $user = new User();

        $this->assertEquals(array(), $user->getActivities());
    }

    public function testSerialize()
    {
        $user = new User();
        $user->setUsername('username');
        $user->setEmail('username@bts.dev');
        $user->setPassword('pass');
        $user->setAvatar('http://avatar');

        $this->assertEquals('a:4:{i:0;N;i:1;s:8:"username";i:2;s:4:"pass";i:3;N;}', $user->serialize());
    }

    public function testUnserialize()
    {
        $user = new User();

        $this->assertEquals(0, $user->getId());
        $this->assertNull($user->getEmail());
        $this->assertNull($user->getUsername());
        $this->assertNull($user->getPassword());
        $this->assertNull($user->getSalt());

        $user->unserialize('a:4:{i:0;N;i:1;s:8:"username";i:2;s:4:"pass";i:3;N;}');

        $this->assertNull($user->getEmail());
        $this->assertNull($user->getSalt());
        $this->assertEquals('username', $user->getUsername());
        $this->assertEquals('pass', $user->getPassword());
    }

    /**
     * @dataProvider isEqualToProvider
     */
    public function testIsEqualTo($username, $password, $expected)
    {
        $user = new User();

        $user->setUsername('user1');
        $user->setPassword('user1@dev.com');

        $user1 = new User();

        $user1->setUsername($username);
        $user1->setPassword($password);

        $this->assertEquals($expected, $user->isEqualTo($user1));
    }

    public function isEqualToProvider()
    {
        return array(
            array('user1', 'user1@dev.com', true),
            array('user2', 'user1@dev.com', false),
            array('user1', 'user2@dev.com', false),
        );
    }

    public function testAddRoleFunction()
    {
        $user = new User();

        $this->assertCount(0, $user->getRoles());

        $role = new Role();

        $user->addRole($role);

        $this->assertCount(1, $user->getRoles());
    }

    public function testAddRolesFunction()
    {
        $user = new User();

        $this->assertCount(0, $user->getRoles());

        $role1 = new Role();
        $role2 = new Role();
        $role3 = new Role();

        $user->addRoles(array($role1, $role2, $role3));

        $this->assertCount(3, $user->getRoles());
    }

    public function testRemoveRoleFunction()
    {
        $user = new User();

        $this->assertCount(0, $user->getRoles());

        $role = new Role();

        $user->addRole($role);

        $this->assertCount(1, $user->getRoles());

        $user->removeRole($role);

        $this->assertCount(0, $user->getRoles());
    }
}
