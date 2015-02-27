<?php

namespace Dte\BtsBundle\Tests\Unit\EventListener;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\User;
use Dte\BtsBundle\EventListener\IssueListener;

class IssueListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    public function setUp()
    {
        $this->tokenStorage =
            $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage')
                ->disableOriginalConstructor()
                ->setMethods(array('getToken'))
                ->getMock();
    }

    public function tearDown()
    {
        $this->tokenStorage = null;
    }

    public function testPostPersistIssue()
    {
        $issue = new Issue();

        $args = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->setMethods(array('getEntity'))
            ->getMock();

        $args->expects($this->once())->method('getEntity')->will($this->returnValue($issue));

        $listener = $this->getMockBuilder('Dte\BtsBundle\EventListener\IssueListener')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'addIssueCode',
                'addIssueReporterCollaborator',
                'addIssueAssigneeCollaborator',
                'addCommentCollaborator'
            ))
            ->getMock();

        $listener
            ->expects($this->once())
            ->method('addIssueCode')
            ->with($this->equalTo($issue));
        $listener
            ->expects($this->once())
            ->method('addIssueReporterCollaborator')
            ->with($this->equalTo($issue));
        $listener
            ->expects($this->once())
            ->method('addIssueAssigneeCollaborator')
            ->with($this->equalTo($issue));
        $listener
            ->expects($this->never())
            ->method('addCommentCollaborator');

        $listener->postPersist($args);
    }

    public function testPostPersistComment()
    {
        $comment = new Comment();

        $args = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->setMethods(array('getEntity'))
            ->getMock();

        $args->expects($this->once())->method('getEntity')->will($this->returnValue($comment));

        $listener = $this->getMockBuilder('Dte\BtsBundle\EventListener\IssueListener')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'addIssueCode',
                'addIssueReporterCollaborator',
                'addIssueAssigneeCollaborator',
                'addCommentCollaborator'
            ))
            ->getMock();

        $listener
            ->expects($this->never())
            ->method('addIssueCode');
        $listener
            ->expects($this->never())
            ->method('addIssueReporterCollaborator');
        $listener
            ->expects($this->never())
            ->method('addIssueAssigneeCollaborator');
        $listener
            ->expects($this->once())
            ->method('addCommentCollaborator')
            ->with($this->equalTo($comment));

        $listener->postPersist($args);
    }

    public function testPrePersistIssue()
    {
        $issue = new Issue();

        $args = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->setMethods(array('getEntity'))
            ->getMock();

        $args->expects($this->once())->method('getEntity')->will($this->returnValue($issue));

        $listener = $this->getMockBuilder('Dte\BtsBundle\EventListener\IssueListener')
            ->disableOriginalConstructor()
            ->setMethods(array('addIssueReporterAndAssignee'))
            ->getMock();

        $listener
            ->expects($this->once())
            ->method('addIssueReporterAndAssignee')
            ->with($this->equalTo($issue));

        $listener->prePersist($args);
    }

    public function testPreUpdateIssueAssigneeChanged()
    {
        $issue = new Issue();

        $args = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->setMethods(array('getEntity', 'hasChangedField'))
            ->getMock();

        $args
            ->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($issue));
        $args
            ->expects($this->once())
            ->method('hasChangedField')
            ->with($this->equalTo('assignee'))
            ->will($this->returnValue(true));

        $listener = $this->getMockBuilder('Dte\BtsBundle\EventListener\IssueListener')
            ->disableOriginalConstructor()
            ->setMethods(array('addIssueAssigneeCollaborator'))
            ->getMock();

        $listener
            ->expects($this->once())
            ->method('addIssueAssigneeCollaborator')
            ->with($this->equalTo($issue));

        $listener->preUpdate($args);
    }

    public function testPreUpdateIssueAssigneeNotChanged()
    {
        $issue = new Issue();

        $args = $this->getMockBuilder('Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->setMethods(array('getEntity', 'hasChangedField'))
            ->getMock();

        $args
            ->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($issue));
        $args
            ->expects($this->once())
            ->method('hasChangedField')
            ->with($this->equalTo('assignee'))
            ->will($this->returnValue(false));

        $listener = $this->getMockBuilder('Dte\BtsBundle\EventListener\IssueListener')
            ->disableOriginalConstructor()
            ->setMethods(array('addIssueAssigneeCollaborator'))
            ->getMock();

        $listener
            ->expects($this->never())
            ->method('addIssueAssigneeCollaborator');

        $listener->preUpdate($args);
    }

    public function testAddIssueCode()
    {
        $issue = $this->getMockBuilder('Dte\BtsBundle\Entity\Issue')
            ->setMethods(array('generateCode', 'setCode'))
            ->getMock();

        $issue
            ->expects($this->once())
            ->method('generateCode');
        $issue
            ->expects($this->once())
            ->method('setCode');

        $listener = new IssueListener($this->tokenStorage);
        $listener->addIssueCode($issue);
    }

    public function testAddIssueReporterAndAssignee()
    {
        $issue = $this->getMockBuilder('Dte\BtsBundle\Entity\Issue')
            ->setMethods(array('setReporter', 'setAssignee'))
            ->getMock();

        $issue
            ->expects($this->once())
            ->method('setAssignee');
        $issue
            ->expects($this->once())
            ->method('setReporter');

        $listener = $this->getMockBuilder('Dte\BtsBundle\EventListener\IssueListener')
            ->disableOriginalConstructor()
            ->setMethods(array('getUser'))
            ->getMock();

        $listener
            ->expects($this->exactly(2))
            ->method('getUser');

        $listener->addIssueReporterAndAssignee($issue);
    }

    public function testPostFlush()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(array('flush'))
            ->getMock();

        $em
            ->expects($this->once())
            ->method('flush');

        $args = $this->getMockBuilder('Doctrine\ORM\Event\PostFlushEventArgs')
            ->disableOriginalConstructor()
            ->setMethods(array('getEntityManager'))
            ->getMock();

        $args
            ->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($em));

        $issue = new Issue();
        $user  = new User();
        $user->setEmail('sd@email');

        $comment = new Comment();
        $comment->setIssue($issue);
        $comment->setUser($user);

        $listener = new IssueListener($this->tokenStorage);
        $listener->addCommentCollaborator($comment);
        $listener->postFlush($args);
    }
}
