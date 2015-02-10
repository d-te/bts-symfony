<?php

namespace Dte\BtsBundle\Tests\Unit\Entity;

use Dte\BtsBundle\Entity\Activity;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\User;

class ActivityTest extends \PHPUnit_Framework_TestCase
{

    public function testMessageSetterGetter()
    {
        $activity = new Activity();

        $this->assertNull($activity->getMessage());

        $activity->setMessage('Message');

        $this->assertEquals('Message', $activity->getMessage());
    }

    public function testIssueSetterGetter()
    {
        $activity = new Activity();

        $this->assertNull($activity->getIssue());

        $issue = new Issue();

        $activity->setIssue($issue);

        $this->assertEquals($issue, $activity->getIssue());
    }

    public function testUserSetterGetter()
    {
        $activity = new Activity();

        $this->assertNull($activity->getUser());

        $user = new User();

        $activity->setUser($user);

        $this->assertEquals($user, $activity->getUser());
    }
}
