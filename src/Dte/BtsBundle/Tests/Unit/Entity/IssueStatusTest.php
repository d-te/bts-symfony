<?php

namespace Dte\BtsBundle\Tests\Unit\Entity;

use Dte\BtsBundle\Entity\IssueStatus;

class IssueStatusTest extends \PHPUnit_Framework_TestCase
{

    public function testLabelSetterGetter()
    {
        $entity = new IssueStatus();

        $this->assertNull($entity->getLabel());

        $entity->setLabel('Label');

        $this->assertEquals('Label', $entity->getLabel());
    }

    public function testOrderSetterGetter()
    {
        $entity = new IssueStatus();

        $this->assertNull($entity->getOrder());

        $entity->setOrder(10);

        $this->assertEquals(10, $entity->getOrder());
    }
}
