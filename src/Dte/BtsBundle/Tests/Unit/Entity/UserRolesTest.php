<?php

namespace Dte\BtsBundle\Tests\Unit\Entity;

use Dte\BtsBundle\Entity\User;
use Dte\BtsBundle\Entity\Role;
use Dte\BtsBundle\Entity\UserRoles;

class UserRolesTest extends \PHPUnit_Framework_TestCase
{

    public function testUserSetterGetter()
    {
        $relation = new UserRoles();

        $this->assertNull($relation->getUser());

        $user = new User();

        $relation->setUser($user);

        $this->assertEquals($user, $relation->getUser());
    }

    public function testRoleSetterGetter()
    {
        $relation = new UserRoles();

        $this->assertNull($relation->getRole());

        $role = new Role();

        $relation->setRole($role);

        $this->assertEquals($role, $relation->getRole());
    }
}
