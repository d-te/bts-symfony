<?php

namespace Dte\BtsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IssueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('summary')
            ->add('code')
            ->add('description')
            ->add('type')
            ->add('created')
            ->add('updated')
            ->add('parent')
            ->add('reporter')
            ->add('project')
            ->add('status')
            ->add('assignee')
            ->add('priority')
            ->add('resolution')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dte\BtsBundle\Entity\Issue'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dte_btsbundle_issue';
    }
}
