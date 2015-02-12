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

        $members = array();
        $stories = array();

        if ($isCreateContext) {
            $builder->add('reporter', 'hidden', array('data' => $user));
        } else {
            $builder->add('reporter', 'hidden');
        }

        if ($isEditContext) {
            $em = $this->getDoctrine()->getManager();
            $project = $em->getRepository('DteBtsBundle:Project')->find($id);

            $members = $project->getMembers();
            $stories = $em->getRepository('DteBtsBundle:Issue')->findStoriesByProject($project);
        }

        $builder
            ->add('project', 'entity', array(
                'required'      => true,
                'disabled'      => $isEditContext,
                'property'      => 'selectLabel',
                'class'         => 'DteBtsBundle:Project',
                'empty_value'   => 'Select a project',
                'query_builder' => function(EntityRepository $em) use ($user) {
                    return $em->findByMemberQueryBuilder($user);
                },
            ));

        if ($isEditContext) {
            $builder
                ->add('code', 'text', array('required' => false, 'disabled' => true));
        }

        $builder
            ->add('type', 'choice', array(
                'disabled' => $isEditContext,
                'choices'  => array(
                    1 => 'Bug',
                    2 => 'Task',
                    3 => 'Story',
                    4 => 'Subtask',
                ),
                'data' => 2,
            ))
            ->add('summary', 'text', array('required' => true))
            ->add('description', 'textarea', array('required' => false))
            ->add('parent', 'entity', array(
                'required'      => false,
                'property'      => 'selectLabel',
                'class'         => 'DteBtsBundle:Issue',
                'empty_value'   => 'Select an parent issue',
                'choices'       => $stories,
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
            ->add('assignee', 'entity', array(
                'required'      => false,
                'property'      => 'fullname',
                'class'         => 'DteBtsBundle:User',
                'empty_value'   => 'Select a assignee',
                'choices'       => $members,
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
