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
            ->add('code', 'text', array('required' => true))
            ->add('label', 'text', array('required' => true))
            ->add('summary', 'textarea', array('required' => true))
            ->add('members', 'bootstrap_collection', array(
                'type'               => 'entity',
                'allow_add'          => true,
                'allow_delete'       => true,
                'add_button_text'    => 'Add member',
                'delete_button_text' => 'Delete member',
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
            'data_class' => 'Dte\BtsBundle\Entity\Project'
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
