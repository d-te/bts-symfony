<?php

namespace Dte\BtsBundle\Tests\Unit\Form;

use Dte\BtsBundle\Entity\User;

class IssueTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $em;

    /**
     * @var \Dte\BtsBundle\Form\IssueType
     */
    private $form;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
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
            'form_context' => 'create',
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
