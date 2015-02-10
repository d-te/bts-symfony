<?php

namespace Dte\BtsBundle\Tests\Unit\Entity;

use Dte\BtsBundle\Entity\Role;

use Symfony\Component\Security\Core\Role\RoleInterface;

class RoleTest extends \PHPUnit_Framework_TestCase
{

    public function testImplementsRoleInterface()
    {
        $role = new Role();

        $this->assertTrue($role instanceof RoleInterface);
    }

    public function testNameSetterGetter()
    {
        $role = new Role();

        $this->assertNull($role->getName());

        $role->setName('test');

        $this->assertEquals('test', $role->getName());
    }

    public function testRoleSetterGetter()
    {
        $role = new Role();

        $this->assertNull($role->getRole());

        $role->setRole('test');

        $this->assertEquals('test', $role->getRole());
    }
}
