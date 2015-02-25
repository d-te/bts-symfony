<?php

namespace Dte\BtsBundle\Tests\Unit\Manager;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\IssueStatus;
use Dte\BtsBundle\Entity\User;

class ActivityManagerTest extends \PHPUnit_Framework_TestCase
{

    private $container;

    public function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
                        ->disableOriginalConstructor()
                        ->setMethods(array(
                            'get',
                            'getParameter',
                        ))
                        ->getMock();
    }

    public function tearDown()
    {
        $this->container = null;
    }

    public function testLogPersistIssue()
    {
        $issue = new Issue();

        $user = new User();

        $issue->setReporter($user);

        $manager = $this->getMockBuilder('Dte\BtsBundle\Manager\ActivityManager')
                        ->disableOriginalConstructor()
                        ->setMethods(array('getUser', 'addActivity'))
                        ->getMock();

         $manager
                ->expects($this->once())
                ->method('addActivity')
                ->with($this->equalTo('New issue added'), $this->equalTo($issue), $this->equalTo($user));

        $manager->logPersistIssue($issue);
    }

    public function testLogUpdateIssueStatus()
    {
        $issue = new Issue();

        $status = new IssueStatus();
        $status->setLabel('Open');

        $issue->setStatus($status);

        $user = new User();

        $manager = $this->getMockBuilder('Dte\BtsBundle\Manager\ActivityManager')
                        ->disableOriginalConstructor()
                        ->setMethods(array('getUser', 'addActivity'))
                        ->getMock();

        $manager
                ->expects($this->once())
                ->method('getUser')
                ->will($this->returnValue($user));

        $manager
                ->expects($this->once())
                ->method('addActivity')
                ->with($this->equalTo('Issue status changed to Open'), $this->equalTo($issue), $this->equalTo($user));

        $manager->logUpdateIssueStatus($issue);
    }

    public function testLogPersistComment()
    {
        $issue = new Issue();
        $user  = new User();

        $comment = new Comment();
        $comment->setIssue($issue);
        $comment->setUser($user);

        $manager = $this->getMockBuilder('Dte\BtsBundle\Manager\ActivityManager')
                        ->disableOriginalConstructor()
                        ->setMethods(array('getUser', 'addActivity'))
                        ->getMock();

        $manager
                ->expects($this->once())
                ->method('addActivity')
                ->with($this->equalTo('New comment added'), $this->equalTo($issue), $this->equalTo($user));

        $manager->logPersistComment($comment);
    }

    public function testSaveActivities()
    {
        $issue = new Issue();
        $user  = new User();

        $em = $this->getMockBuilder('Dte\BtsBundle\Manager\ActivityManager')
                        ->disableOriginalConstructor()
                        ->setMethods(array('persist', 'flush'))
                        ->getMock();
        $em
            ->expects($this->once())
            ->method('persist');

        $em
            ->expects($this->once())
            ->method('flush');

        $manager = $this->getMockBuilder('Dte\BtsBundle\Manager\ActivityManager')
                        ->disableOriginalConstructor()
                        ->setMethods(array('getEntityManager'))
                        ->getMock();
        $manager
                ->expects($this->once())
                ->method('getEntityManager')
                ->will($this->returnValue($em));

        $manager->addActivity('message', $issue, $user);

        $manager->saveActivities();
    }
}
