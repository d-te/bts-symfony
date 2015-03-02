<?php

namespace Dte\BtsBundle\Tests\Unit\Entity;

use Dte\BtsBundle\Entity\IssueTaskType;

class IssueTaskTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testGetItems()
    {
        $expected = array(
            IssueTaskType::BUG_TYPE     => 'Bug',
            IssueTaskType::TASK_TYPE    => 'Task',
            IssueTaskType::STORY_TYPE   => 'Story',
            IssueTaskType::SUBTASK_TYPE => 'Subtask',
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
            array(IssueTaskType::BUG_TYPE, 'Bug'),
            array(IssueTaskType::TASK_TYPE, 'Task'),
            array(IssueTaskType::STORY_TYPE, 'Story'),
            array(IssueTaskType::SUBTASK_TYPE, 'Subtask'),
        );
    }
}
