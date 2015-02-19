<?php

namespace Dte\BtsBundle\Tests\Unit\Form;

use Dte\BtsBundle\Form\UserType;

class UserTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testGetName()
    {
        $form = new UserType();

        $this->assertEquals('dte_btsbundle_user', $form->getName());
    }

    public function testSetDefaultOptions()
    {
        $options = array(
            'data_class'   => 'Dte\BtsBundle\Entity\User',
            'form_context' => 'default',
        );

        $resolver = $this->getMockBuilder('Symfony\Component\OptionsResolver\OptionsResolver')
                        ->disableOriginalConstructor()
                        ->setMethods(array('setDefaults'))
                        ->getMock();

        $resolver
                ->expects($this->once())
                ->method('setDefaults')
                ->with($this->equalTo($options));

        $form = new UserType();
        $form->setDefaultOptions($resolver);
    }

    public function testBuildFormCreateContext()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
                        ->disableOriginalConstructor()
                        ->setMethods(array('add'))
                        ->getMock();
        $builder
                ->expects($this->at(0))
                ->method('add')
                ->with(
                    $this->equalTo('email'),
                    $this->equalTo('email'),
                    $this->equalTo(
                        array(
                            'required'  => true,
                            'read_only' => false,
                            'label'     => 'bts.entity.user.email.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(1))
                ->method('add')
                ->with(
                    $this->equalTo('username'),
                    $this->equalTo('text'),
                    $this->equalTo(
                        array(
                            'required'  => true,
                            'label'     => 'bts.entity.user.username.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(2))
                ->method('add')
                ->with(
                    $this->equalTo('fullname'),
                    $this->equalTo('text'),
                    $this->equalTo(
                        array(
                            'required'  => true,
                            'label'     => 'bts.entity.user.fullname.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(3))
                ->method('add')
                ->with(
                    $this->equalTo('password'),
                    $this->equalTo('password'),
                    $this->equalTo(
                        array(
                            'required'  => true,
                            'label'     => 'bts.entity.user.password.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(4))
                ->method('add')
                ->with(
                    $this->equalTo('avatar'),
                    $this->equalTo('url'),
                    $this->equalTo(
                        array(
                            'required'  => false,
                            'label'     => 'bts.entity.user.avatar.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));

        $form = new UserType();
        $form->buildForm($builder, array('form_context' => 'create'));
    }

    public function testBuildFormProfileContext()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
                        ->disableOriginalConstructor()
                        ->setMethods(array('add'))
                        ->getMock();
        $builder
                ->expects($this->at(0))
                ->method('add')
                ->with(
                    $this->equalTo('email'),
                    $this->equalTo('email'),
                    $this->equalTo(
                        array(
                            'required'  => true,
                            'read_only' => true,
                            'label'     => 'bts.entity.user.email.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(1))
                ->method('add')
                ->with(
                    $this->equalTo('username'),
                    $this->equalTo('text'),
                    $this->equalTo(
                        array(
                            'required'  => true,
                            'label'     => 'bts.entity.user.username.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(2))
                ->method('add')
                ->with(
                    $this->equalTo('fullname'),
                    $this->equalTo('text'),
                    $this->equalTo(
                        array(
                            'required'  => true,
                            'label'     => 'bts.entity.user.fullname.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(3))
                ->method('add')
                ->with(
                    $this->equalTo('password'),
                    $this->equalTo('password'),
                    $this->equalTo(
                        array(
                            'required'  => false,
                            'label'     => 'bts.entity.user.password.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(4))
                ->method('add')
                ->with(
                    $this->equalTo('avatar'),
                    $this->equalTo('url'),
                    $this->equalTo(
                        array(
                            'required'  => false,
                            'label'     => 'bts.entity.user.avatar.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));

        $form = new UserType();
        $form->buildForm($builder, array('form_context' => 'profile'));
    }

    public function testBuildFormEditContext()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
                        ->disableOriginalConstructor()
                        ->setMethods(array('add'))
                        ->getMock();
        $builder
                ->expects($this->at(0))
                ->method('add')
                ->with(
                    $this->equalTo('email'),
                    $this->equalTo('email'),
                    $this->equalTo(
                        array(
                            'required'  => true,
                            'read_only' => true,
                            'label'     => 'bts.entity.user.email.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(1))
                ->method('add')
                ->with(
                    $this->equalTo('username'),
                    $this->equalTo('text'),
                    $this->equalTo(
                        array(
                            'required'  => true,
                            'label'     => 'bts.entity.user.username.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(2))
                ->method('add')
                ->with(
                    $this->equalTo('fullname'),
                    $this->equalTo('text'),
                    $this->equalTo(
                        array(
                            'required'  => true,
                            'label'     => 'bts.entity.user.fullname.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(3))
                ->method('add')
                ->with(
                    $this->equalTo('password'),
                    $this->equalTo('password'),
                    $this->equalTo(
                        array(
                            'required'  => false,
                            'label'     => 'bts.entity.user.password.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(4))
                ->method('add')
                ->with(
                    $this->equalTo('avatar'),
                    $this->equalTo('url'),
                    $this->equalTo(
                        array(
                            'required'  => false,
                            'label'     => 'bts.entity.user.avatar.label'
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(5))
                ->method('add')
                ->with(
                    $this->equalTo('roles'),
                    $this->equalTo('entity'),
                    $this->equalTo(
                        array(
                            'required' => true,
                            'label'     => 'bts.entity.user.roles.label',
                            'property' => 'name',
                            'class'    => 'DteBtsBundle:Role',
                            'multiple' => true,
                            'expanded' => true,
                        )
                    )
                )
                ->will($this->returnValue($builder));

        $form = new UserType();
        $form->buildForm($builder, array('form_context' => 'edit'));
    }
}
