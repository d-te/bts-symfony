<?php

namespace Dte\BtsBundle\Tests\Unit\Form;

use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\Project;
use Dte\BtsBundle\Entity\User;

use Dte\BtsBundle\Form\IssueType;

class IssueTypeTest extends \PHPUnit_Framework_TestCase
{

    private $em;
    private $form;
    private $securityContext;

    public function setUp()
    {
        $this->securityContext = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
                        ->disableOriginalConstructor()
                        ->setMethods(array('getToken'))
                        ->getMock();

        $this->em = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->form = $this->getMockBuilder('Dte\BtsBundle\Form\IssueType')
                        ->setConstructorArgs(array($this->securityContext, $this->em))
                        ->setMethods(array('getUser', 'getProjectMembers', 'getProjectStories'))
                        ->getMock();

        $this->form
                ->expects($this->any())
                ->method('getUser')
                ->will($this->returnValue(new User()));

        $this->form
                ->expects($this->any())
                ->method('getProjectMembers')
                ->will($this->returnValue(array()));

        $this->form
                ->expects($this->any())
                ->method('getProjectStories')
                ->will($this->returnValue(array()));

    }

    public function tearDown()
    {
        $this->form            = null;
        $this->securityContext = null;
        $this->em              = null;
    }

    public function testGetName()
    {
        $this->assertEquals('dte_btsbundle_issue', $this->form->getName());
    }

    public function testSetDefaultOptions()
    {
        $options = array(
            'data_class'   => 'Dte\BtsBundle\Entity\Issue',
            'form_context' => 'default',
            'isSubtask'    => false,
        );

        $resolver = $this->getMockBuilder('Symfony\Component\OptionsResolver\OptionsResolver')
                        ->disableOriginalConstructor()
                        ->setMethods(array('setDefaults'))
                        ->getMock();

        $resolver
                ->expects($this->once())
                ->method('setDefaults')
                ->with($this->equalTo($options));

        $this->form->setDefaultOptions($resolver);
    }
}
