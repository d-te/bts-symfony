<?php

namespace Dte\BtsBundle\Tests\Unit\EventListener;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\EventListener\ActivitySubscriber;

class ActivitySubscriberTest extends \PHPUnit_Framework_TestCase
{

    private $activityManager;

    public function setUp()
    {
        $this->activityManager = $this->getMockBuilder('Dte\BtsBundle\Manager\ActivityManager')
                         ->disableOriginalConstructor()
                         ->setMethods(array(
                            'logPersistIssue',
                            'logPersistComment',
                            'logUpdateIssueStatus',
                            'saveActivities',
                         ))
                         ->getMock();
    }

    public function tearDown()
    {
        $this->activityManager = null;
    }

    public function testGetSubscribedEvents()
    {
        $expected = array(
            'postPersist',
            'preUpdate',
            'postFlush',
        );

        $subscriber = new ActivitySubscriber($this->activityManager);

        $this->assertEquals($expected, $subscriber->getSubscribedEvents());
    }

    public function testPostPersistLogIssue()
    {
        $issue = new Issue();

        $args = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
                            ->disableOriginalConstructor()
                            ->setMethods(array('getEntity'))
                            ->getMock();

        $args->expects($this->once())->method('getEntity')->will($this->returnValue($issue));

        $this->activityManager->expects($this->once())->method('logPersistIssue')->with($this->equalTo($issue));

        $subscriber = new ActivitySubscriber($this->activityManager);

        $subscriber->postPersist($args);
    }

    public function testPostPersistLogComment()
    {
        $comment = new Comment();

        $args = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
                            ->disableOriginalConstructor()
                            ->setMethods(array('getEntity'))
                            ->getMock();

        $args->expects($this->once())->method('getEntity')->will($this->returnValue($comment));

        $this->activityManager->expects($this->once())->method('logPersistComment')->with($this->equalTo($comment));

        $subscriber = new ActivitySubscriber($this->activityManager);

        $subscriber->postPersist($args);
    }

    public function testPreUpdateLogIssueStatusStatusChanged()
    {
        $issue = new Issue();

        $args = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
                            ->disableOriginalConstructor()
                            ->setMethods(array(
                                'getEntity',
                                'hasChangedField'
                            ))
                            ->getMock();

        $args
            ->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($issue));
        $args
            ->expects($this->once())
            ->method('hasChangedField')
            ->with($this->equalTo('status'))
            ->will($this->returnValue(true));

        $this->activityManager
                ->expects($this->once())
                ->method('logUpdateIssueStatus')
                ->with($this->equalTo($issue));

        $subscriber = new ActivitySubscriber($this->activityManager);

        $subscriber->preUpdate($args);
    }

    public function testPreUpdateLogIssueStatusStatusNotChanged()
    {
        $issue = new Issue();

        $args = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
                            ->disableOriginalConstructor()
                            ->setMethods(array(
                                'getEntity',
                                'hasChangedField'
                            ))
                            ->getMock();

        $args
            ->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($issue));
        $args
            ->expects($this->once())
            ->method('hasChangedField')
            ->with($this->equalTo('status'))
            ->will($this->returnValue(false));

        $this->activityManager->expects($this->never())->method('logUpdateIssueStatus');

        $subscriber = new ActivitySubscriber($this->activityManager);

        $subscriber->preUpdate($args);
    }

    public function testPostFlush()
    {
        $args = $this->getMockBuilder('Doctrine\ORM\Event\PostFlushEventArgs')
                            ->disableOriginalConstructor()
                            ->getMock();

        $this->activityManager->expects($this->once())->method('saveActivities');

        $subscriber = new ActivitySubscriber($this->activityManager);

        $subscriber->postFlush($args);
    }
}
