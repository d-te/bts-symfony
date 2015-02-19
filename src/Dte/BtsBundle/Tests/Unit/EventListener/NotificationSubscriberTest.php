<?php

namespace Dte\BtsBundle\Tests\Unit\EventListener;

use Dte\BtsBundle\Entity\Activity;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\User;
use Dte\BtsBundle\EventListener\NotificationSubscriber;

class NotificationSubscriberTest extends \PHPUnit_Framework_TestCase
{

    private $container;

    private $templateEngine;

    private $mailer;

    public function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
                         ->disableOriginalConstructor()
                         ->setMethods(array(
                            'get',
                            'getParameter',
                         ))
                         ->getMock();

        $this->templateEngine = $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
                         ->disableOriginalConstructor()
                         ->setMethods(array('render'))
                         ->getMock();

        $this->mailer = $this->getMockBuilder('\Swift_Mailer')
                         ->disableOriginalConstructor()
                         ->setMethods(array('send', 'createMessage'))
                         ->getMock();
    }

    public function tearDown()
    {
        $this->container      = null;
        $this->mailer         = null;
        $this->templateEngine = null;
    }

    public function testGetSubscribedEvents()
    {
        $expected = array(
            'postPersist',
        );

        $subscriber = new NotificationSubscriber($this->container);

        $this->assertEquals($expected, $subscriber->getSubscribedEvents());
    }
}
