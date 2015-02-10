<?php

namespace Dte\BtsBundle\Tests\Unit\Entity;

use Dte\BtsBundle\Entity\IssueResolution;

class IssueResolutionTest extends \PHPUnit_Framework_TestCase
{

    public function testLabelSetterGetter()
    {
        $entity = new IssueResolution();

        $this->assertNull($entity->getLabel());

        $entity->setLabel('Label');

        $this->assertEquals('Label', $entity->getLabel());
    }

    public function testOrderSetterGetter()
    {
        $entity = new IssueResolution();

        $this->assertNull($entity->getOrder());

        $entity->setOrder(10);

        $this->assertEquals(10, $entity->getOrder());
    }
}
