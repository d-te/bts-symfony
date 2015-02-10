<?php

namespace Dte\BtsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array('required' => true, 'disabled' => true))
            ->add('username', 'text', array('required' => true, 'label' => 'Nickname'))
            ->add('fullname', 'text', array('required' => true))
            ->add('password', 'password', array('required' => false))
            ->add('avatar', 'url', array('required' => false))
            ->add('roles', 'entity', array(
                'required' => true,
                'property' => 'name',
                'class'    => 'DteBtsBundle:Role',
                'multiple' => true,
                'expanded' => true,
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dte\BtsBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dte_btsbundle_user';
    }
}
