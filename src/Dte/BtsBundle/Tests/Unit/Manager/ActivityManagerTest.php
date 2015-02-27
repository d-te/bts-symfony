<?php

namespace Dte\BtsBundle\Tests\Unit\Manager;

use Dte\BtsBundle\Entity\Activity;
use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\IssueStatus;
use Dte\BtsBundle\Entity\User;

class ActivityManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     *  @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Dte\BtsBundle\Manager\ActivityManager
     */
    private $manager;

    public function setUp()
    {
        $this->doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('getManager'))
            ->getMock();
        $this->tokenStorage =
            $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage')
                ->disableOriginalConstructor()
                ->setMethods(array('getToken'))
                ->getMock();
        $this->mailer = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()
            ->setMethods(array('createMessage', 'send'))
            ->getMock();
        $this->translator = $this->getMockBuilder('Symfony\Component\Translation\Translator')
            ->disableOriginalConstructor()
            ->setMethods(array('trans'))
            ->getMock();
        $this->router = $this->getMockBuilder('Symfony\Component\Routing\Router')
            ->disableOriginalConstructor()
            ->setMethods(array('generate'))
            ->getMock();
        $this->manager = $this->getMockBuilder('Dte\BtsBundle\Manager\ActivityManager')
            ->setConstructorArgs(array(
                $this->tokenStorage,
                $this->doctrine,
                $this->mailer,
                $this->translator,
                $this->router,
                'noreplay@email'
            ))
            ->setMethods(array('getUser', 'addActivity', 'getEntityManager'))
            ->getMock();
    }

    public function tearDown()
    {
        $this->doctrine     = null;
        $this->tokenStorage = null;
        $this->mailer       = null;
        $this->translator   = null;
        $this->router       = null;
        $this->manager      = null;
    }

    public function testLogPersistIssue()
    {
        $issue = new Issue();

        $user = new User();

        $issue->setReporter($user);

        $this->manager
            ->expects($this->once())
            ->method('addActivity')
            ->with($this->equalTo('New issue added'), $this->equalTo($issue), $this->equalTo($user));

        $this->manager->logPersistIssue($issue);
    }

    public function testLogUpdateIssueStatus()
    {
        $issue = new Issue();

        $status = new IssueStatus();
        $status->setLabel('Open');

        $issue->setStatus($status);

        $user = new User();

        $this->manager
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($user));

        $this->manager
            ->expects($this->once())
            ->method('addActivity')
            ->with($this->equalTo('Issue status changed to Open'), $this->equalTo($issue), $this->equalTo($user));

        $this->manager->logUpdateIssueStatus($issue);
    }

    public function testLogPersistComment()
    {
        $issue = new Issue();
        $user  = new User();

        $comment = new Comment();
        $comment->setIssue($issue);
        $comment->setUser($user);

        $this->manager
            ->expects($this->once())
            ->method('addActivity')
            ->with($this->equalTo('New comment added'), $this->equalTo($issue), $this->equalTo($user));

        $this->manager->logPersistComment($comment);
    }

    public function testSaveActivities()
    {
        $issue = new Issue();
        $user  = new User();

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(array('persist', 'flush'))
            ->getMock();
        $em
            ->expects($this->once())
            ->method('persist');

        $em
            ->expects($this->once())
            ->method('flush');

        $this->manager = $this->getMockBuilder('Dte\BtsBundle\Manager\ActivityManager')
            ->setConstructorArgs(array(
                $this->tokenStorage,
                $this->doctrine,
                $this->mailer,
                $this->translator,
                $this->router,
                'admin@email'
            ))
            ->setMethods(array('getUser', 'getEntityManager', 'sendCollaboratorsNotification'))
            ->getMock();

        $this->manager
            ->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($em));

        $this->manager
            ->expects($this->once())
            ->method('sendCollaboratorsNotification');

        $this->manager->addActivity('message', $issue, $user);

        $this->manager->saveActivities();
    }

    public function testSendCollaboratorsNotification()
    {

        $user  = new User();
        $user->setEmail('admin@email');

        $issue = new Issue();

        $user1  = new User();
        $user1->setEmail('user@email');

        $issue->addCollaborator($user1);
        $issue->setCode('BBB-1');

        $activity = new Activity();
        $activity->setIssue($issue);
        $activity->setUser($user);
        $activity->setMessage('message');

        $this->router
            ->expects($this->exactly(2))
            ->method('generate');

        $this->translator
            ->expects($this->once())
            ->method('trans');

        $this->mailer
            ->expects($this->once())
            ->method('createMessage')
            ->will($this->returnValue(\Swift_Message::newInstance()));

        $this->mailer
            ->expects($this->once())
            ->method('send');

        $this->manager = $this->getMockBuilder('Dte\BtsBundle\Manager\ActivityManager')
            ->setConstructorArgs(array(
                $this->tokenStorage,
                $this->doctrine,
                $this->mailer,
                $this->translator,
                $this->router,
                'noreplay@email'
            ))
            ->setMethods(array('getUser', 'getEntityManager'))
            ->getMock();

        $this->manager->sendCollaboratorsNotification($activity);
    }
}
