<?php

namespace Dte\BtsBundle\Tests\Unit\Form;

use Dte\BtsBundle\Form\ProjectType;

class ProjectTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testGetName()
    {
        $form = new ProjectType();

        $this->assertEquals('dte_btsbundle_project', $form->getName());
    }

    public function testSetDefaultOptions()
    {
        $options = array(
            'data_class'   => 'Dte\BtsBundle\Entity\Project',
        );

        $resolver = $this->getMockBuilder('Symfony\Component\OptionsResolver\OptionsResolver')
                        ->disableOriginalConstructor()
                        ->setMethods(array('setDefaults'))
                        ->getMock();

        $resolver
                ->expects($this->once())
                ->method('setDefaults')
                ->with($this->equalTo($options));

        $form = new ProjectType();
        $form->setDefaultOptions($resolver);
    }

    public function testBuildForm()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
                        ->disableOriginalConstructor()
                        ->setMethods(array('add'))
                        ->getMock();
        $builder
                ->expects($this->at(0))
                ->method('add')
                ->with(
                    $this->equalTo('code'),
                    $this->equalTo('text'),
                    $this->equalTo(
                        array(
                            'required' => true,
                            'label'    => 'bts.entity.project.code.label',
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(1))
                ->method('add')
                ->with(
                    $this->equalTo('label'),
                    $this->equalTo('text'),
                    $this->equalTo(
                        array(
                            'required' => true,
                            'label'    => 'bts.entity.project.label.label',
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(2))
                ->method('add')
                ->with(
                    $this->equalTo('summary'),
                    $this->equalTo('textarea'),
                    $this->equalTo(
                        array(
                            'required' => true,
                            'label'    => 'bts.entity.project.summary.label',
                        )
                    )
                )
                ->will($this->returnValue($builder));
        $builder
                ->expects($this->at(3))
                ->method('add')
                ->with(
                    $this->equalTo('members'),
                    $this->equalTo('bootstrap_collection'),
                    $this->equalTo(
                        array(
                            'type'               => 'entity',
                            'label'              => 'bts.entity.project.members.label',
                            'allow_add'          => true,
                            'allow_delete'       => true,
                            'add_button_text'    => 'bts.page.project.action.add_member',
                            'delete_button_text' => 'bts.page.project.action.delete_member',
                            'sub_widget_col'     => 4,
                            'button_col'         => 3,
                            'options'            => array(
                                'class' => 'DteBtsBundle:User',
                                'property' => 'fullname'
                            )
                        )
                    )
                )
                ->will($this->returnValue($builder));

        $form = new ProjectType();
        $form->buildForm($builder, array());
    }
}
