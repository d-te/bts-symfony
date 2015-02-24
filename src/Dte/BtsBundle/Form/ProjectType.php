<?php

namespace Dte\BtsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', 'text', array(
                'label'    => 'bts.entity.project.code.label',
            ))
            ->add('label', 'text', array(
                'label'    => 'bts.entity.project.label.label',
            ))
            ->add('summary', 'textarea', array(
                'label'    => 'bts.entity.project.summary.label',
            ))
            ->add('members', 'bootstrap_collection', array(
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
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'Dte\BtsBundle\Entity\Project'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dte_btsbundle_project';
    }
}
