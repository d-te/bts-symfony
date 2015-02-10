<?php

namespace Dte\BtsBundle\Tests\Unit\Entity;

use Dte\BtsBundle\Entity\Project;
use Dte\BtsBundle\Entity\ProjectMembers;
use Dte\BtsBundle\Entity\User;

class ProjectMembersTest extends \PHPUnit_Framework_TestCase
{

    public function testUserSetterGetter()
    {
        $project = new ProjectMembers();

        $this->assertNull($project->getUser());

        $user = new User();

        $project->setUser($user);

        $this->assertEquals($user, $project->getUser());
    }

    public function testProjectSetterGetter()
    {
        $member = new ProjectMembers();

        $this->assertNull($member->getProject());

        $project = new Project();

        $member->setProject($project);

        $this->assertEquals($project, $member->getProject());
    }
}