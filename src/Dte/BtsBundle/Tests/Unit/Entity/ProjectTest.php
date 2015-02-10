<?php

namespace Dte\BtsBundle\Tests\Unit\Entity;

use Dte\BtsBundle\Entity\Project;
use Dte\BtsBundle\Entity\User;

class ProjectTest extends \PHPUnit_Framework_TestCase
{

    public function testLabelSetterGetter()
    {
        $project = new Project();

        $this->assertNull($project->getLabel());

        $project->setLabel('label');

        $this->assertEquals('label', $project->getLabel());
    }

    public function testSummarySetterGetter()
    {
        $project = new Project();

        $this->assertNull($project->getSummary());

        $project->setSummary('Summary');

        $this->assertEquals('Summary', $project->getSummary());
    }

    public function testCodeSetterGetter()
    {
        $project = new Project();

        $this->assertNull($project->getCode());

        $project->setCode('Code');

        $this->assertEquals('Code', $project->getCode());
    }

    public function testMembersGetter()
    {
        $project = new Project();

        $this->assertEquals(array(), $project->getMembers());
    }

    public function testAddMemberFunction()
    {
        $project = new Project();

        $this->assertCount(0, $project->getMembers());

        $user = new User();

        $project->addMember($user);

        $this->assertCount(1, $project->getMembers());
    }

    public function testRemoveMemberFunction()
    {
        $project = new Project();

        $this->assertCount(0, $project->getMembers());

        $user = new User();

        $project->addMember($user);

        $this->assertCount(1, $project->getMembers());

        $project->removeMember($user);

        $this->assertCount(0, $project->getMembers());
    }
}
