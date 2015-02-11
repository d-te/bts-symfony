<?php

namespace Dte\BtsBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

class IssueType extends AbstractType
{

    /**
     * @param SecurityContext
     */
    private $securityContext;

    /**
     *  Constructor
     *
     * @param SecurityContext $securityContext
     */
    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isCreateContext = ($options['form_context'] === 'create');
        $isEditContext   = ($options['form_context'] === 'edit');

        $user = $this->securityContext->getToken()->getUser();

        $builder
            ->add('project', 'entity', array(
                'required'      => true,
                'property'      => 'selectLabel',
                'class'         => 'DteBtsBundle:Project',
                'empty_value'   => 'Select a project',
                'query_builder' => function(EntityRepository $em) use ($user) {
                    return $em->findByMemberQueryBuilder($user);
                },
            ));

        if ($isCreateContext) {
            $builder
                ->add('code', 'text', array('required' => false, 'disabled' => true));
        }

        $builder
            ->add('summary', 'text', array('required' => true))
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
            ->add('parent', 'entity', array( // Only stories from selected project (ajax)
                'required'      => false,
                'property'      => 'label',
                'class'         => 'DteBtsBundle:Issue',
                'empty_value'   => 'Select an parent issue',
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
            ->add('assignee', 'entity', array( //TODO only members from selected project (ajax)
                'required'      => false,
                'property'      => 'fullname',
                'class'         => 'DteBtsBundle:User',
                'empty_value'   => 'Select a assignee',
            ))
        ;

        if ($isEditContext) {
            $builder
                ->add('resolution', 'entity', array(
                'required'      => false,
                'property'      => 'label',
                'class'         => 'DteBtsBundle:IssueResolution',
                'empty_value'   => 'Select a resolution',
                'query_builder' => function(EntityRepository $em) {
                    return $em->createQueryBuilder('i')->orderBy('i.order', 'ASC');
                }));
        }
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
