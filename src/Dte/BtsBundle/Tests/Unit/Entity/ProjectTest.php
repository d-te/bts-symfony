<?php

namespace Dte\BtsBundle\Tests\Unit\Entity;

use Dte\BtsBundle\Entity\Project;

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
}