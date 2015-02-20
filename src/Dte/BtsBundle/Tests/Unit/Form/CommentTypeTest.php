<?php

namespace Dte\BtsBundle\Tests\Unit\Form;

use Dte\BtsBundle\Form\CommentType;

class CommentTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testGetName()
    {
        $form = new CommentType();

        $this->assertEquals('dte_btsbundle_comment', $form->getName());
    }

    public function testSetDefaultOptions()
    {
        $options = array(
            'data_class' => 'Dte\BtsBundle\Entity\Comment'
        );

        $resolver = $this->getMockBuilder('Symfony\Component\OptionsResolver\OptionsResolver')
                        ->disableOriginalConstructor()
                        ->setMethods(array('setDefaults'))
                        ->getMock();

        $resolver
                ->expects($this->once())
                ->method('setDefaults')
                ->with($this->equalTo($options));

        $form = new CommentType();
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
                    $this->equalTo('body'),
                    $this->equalTo('textarea'),
                    $this->equalTo(array('required' => true, 'label' => false))
                );

        $form = new CommentType();
        $form->buildForm($builder, array());
    }
}
