<?php

namespace Dte\BtsBundle\Tests\Unit\Entity;

use Dte\BtsBundle\Entity\IssueTaskType;

class IssueTaskTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testGetItems()
    {
        $expected = array(
            1 => 'Bug',
            2 => 'Task',
            3 => 'Story',
            4 => 'Subtask',
        );

        $this->assertEquals($expected, IssueTaskType::getItems());
    }

    /**
     * @dataProvider getItemLabelProvider
     */
    public function testGetItemLabel($id, $expected)
    {
        $this->assertEquals($expected, IssueTaskType::getItemLabel($id));
    }

    public function getItemLabelProvider()
    {
        return array(
            array(1, 'Bug'),
            array(2, 'Task'),
            array(3, 'Story'),
            array(4, 'Subtask'),
        );
    }
}
