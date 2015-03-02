<?php

namespace Dte\BtsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    const CREATE_CONTEXT  = 'create';

    const EDIT_CONTEXT    = 'edit';

    const PROFILE_CONTEXT = 'profile';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isCreateContext  = ($options['form_context'] === self::CREATE_CONTEXT);
        $isEditContext    = ($options['form_context'] === self::EDIT_CONTEXT);
        $isProfileContext = ($options['form_context'] === self::PROFILE_CONTEXT);

        $builder
            ->add('is_profile', 'hidden', array(
                'mapped' => false,
                'data'   => intval($isProfileContext),
            ))
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
                    'label'    => 'bts.entity.user.roles.label',
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
