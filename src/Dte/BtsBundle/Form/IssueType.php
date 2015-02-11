<?php

namespace Dte\BtsBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IssueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isCreateContext = ($options['form_context'] === 'create');
        $isEditContext   = ($options['form_context'] === 'edit');

        $builder
            ->add('project', 'entity', array( //TODO only member`s porjects
                'required'      => true,
                'property'      => 'selectLabel',
                'class'         => 'DteBtsBundle:Project',
                'empty_value'   => 'Select a project',
                'query_builder' => function(EntityRepository $em) {
                    return $em->createQueryBuilder('p')->orderBy('p.id', 'DESC');
                },
            ))
            ->add('code', 'text', array('required' => false)) // autogenrated, not editable
            ->add('summary', 'textarea', array('required' => true))
            ->add('description', 'textarea', array('required' => false))
            ->add('type', 'choice', array(
                'choices'       => array(
                    1 => 'Bug',
                    2 => 'Task',
                    3 => 'Story',
                    4 => 'Subtask',
                ),
                'data' => 2,
            ))
            ->add('parent', 'entity', array( //TODO only stories types, not editable for some types
                'required'      => false,
                'property'      => 'label',
                'class'         => 'DteBtsBundle:Issue',
                'empty_value'   => 'Select an parent issue',
                'query_builder' => function(EntityRepository $em) {
                    return $em->createQueryBuilder('i')->orderBy('i.id', 'DESC');
                },
            ))
            ->add('status', 'entity', array(
                'required'      => true,
                'property'      => 'label',
                'class'         => 'DteBtsBundle:IssueStatus',
                'query_builder' => function(EntityRepository $em) {
                    return $em->createQueryBuilder('i')->orderBy('i.order', 'ASC');
                },

            ))
            ->add('priority', 'entity', array(
                'required'      => true,
                'property'      => 'label',
                'class'         => 'DteBtsBundle:IssuePriority',
                'query_builder' => function(EntityRepository $em) {
                    return $em->createQueryBuilder('i')->orderBy('i.order', 'ASC');
                },
                'data' => 3,
            ))
            ->add('assignee', 'entity', array( //TODO only project Members
                'required'      => false,
                'property'      => 'fullname',
                'class'         => 'DteBtsBundle:User',
                'empty_value'   => 'Select a assignee',
                'query_builder' => function(EntityRepository $em) {
                    return $em->createQueryBuilder('u')->orderBy('u.fullname', 'ASC');
                },
            ))
            ->add('resolution', 'entity', array( //TODO only to resolve|reopen actions
                'required'      => false,
                'property'      => 'label',
                'class'         => 'DteBtsBundle:IssueResolution',
                'query_builder' => function(EntityRepository $em) {
                    return $em->createQueryBuilder('i')->orderBy('i.order', 'ASC');
                },
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'   => 'Dte\BtsBundle\Entity\Issue',
            'form_context' => 'default',
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
