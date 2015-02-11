<?php

namespace Dte\BtsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $isCreateContext  = ($options['form_context'] === 'create');
        $isEditContext    = ($options['form_context'] === 'edit');
        $isProfileContext = ($options['form_context'] === 'profile');

        $builder
            ->add('email', 'email', array('required' => true, 'disabled' => ($isEditContext || $isProfileContext) ))
            ->add('username', 'text', array('required' => true, 'label' => 'Nickname'))
            ->add('fullname', 'text', array('required' => true))
            ->add('password', 'password', array('required' => $isCreateContext))
            ->add('avatar', 'url', array('required' => false))
        ;

        if (!$isProfileContext) {
            $builder
                ->add('roles', 'entity', array(
                    'required' => true,
                    'property' => 'name',
                    'class'    => 'DteBtsBundle:Role',
                    'multiple' => true,
                    'expanded' => true,
                ));
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'  => 'Dte\BtsBundle\Entity\User',
            'form_context' => 'default',
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
