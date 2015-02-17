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
            ->add('email', 'email', array(
                'required'  => true,
                'read_only' => ($isEditContext || $isProfileContext),
                'label'     => 'bts.entity.user.email.label',
            ))
            ->add('username', 'text', array(
                'required' => true,
                'label'    => 'bts.entity.user.username.label',
            ))
            ->add('fullname', 'text', array(
                'required' => true,
                'label'    => 'bts.entity.user.fullname.label',
            ))
            ->add('password', 'password', array(
                'required' => $isCreateContext,
                'label'    => 'bts.entity.user.password.label',
            ))
            ->add('avatar', 'url', array(
                'required' => false,
                'label'    => 'bts.entity.user.avatar.label',
            ))
        ;

        if (!$isProfileContext) {
            $builder
                ->add('roles', 'entity', array(
                    'required' => true,
                    'label'     => 'bts.entity.user.roles.label',
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
