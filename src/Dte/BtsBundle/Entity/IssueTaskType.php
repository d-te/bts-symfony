<?php

namespace Dte\BtsBundle\Entity;

class IssueTaskType
{
    const BUG_TYPE     = 1;

    const TASK_TYPE    = 2;

    const STORY_TYPE   = 3;

    const SUBTASK_TYPE = 4;

    private static $items = array(
                self::BUG_TYPE     => 'Bug',
                self::TASK_TYPE    => 'Task',
                self::STORY_TYPE   => 'Story',
                self::SUBTASK_TYPE => 'Subtask',
            );

    /**
     * Return list of issue types
     *
     * @return array
     */
    public static function getItems()
    {
        return self::$items;
    }

    /**
     * Get task type label by id
     *
     * @param integer $id
     * @return string
     */
    public static function getItemLabel($id)
    {
        return self::$items[$id];
    }
}
