<?php

namespace Dte\BtsBundle\Form;

use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\IssueTaskType;
use Dte\BtsBundle\Entity\Project;
use Dte\BtsBundle\Entity\Repository\ProjectRepository;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

class IssueType extends AbstractType
{
    const CREATE_CONTEXT = 'create';

    const EDIT_CONTEXT   = 'edit';

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    private $securityContext;

    /**
     *  @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     *  Constructor
     *
     * @param SecurityContext $securityContext
     * @param Doctrine $doctrine
     */
    public function __construct(SecurityContext $securityContext, Doctrine $doctrine)
    {
        $this->securityContext = $securityContext;

        $this->em = $doctrine->getManager();
    }

    /**
     * Get User
     *
     * @return \Dte\BtsBundle\Entity\User
     */
    public function getUser()
    {
        return $this->securityContext->getToken()->getUser();
    }

    /**
     * Get project's members
     *
     * @param  \Dte\BtsBundle\Entity\Project $project
     * @return array
     */
    public function getProjectMembers($project)
    {
        $members = array();

        if (null !== $project) {
            $members = $project->getMembers();
        }

        return $members;
    }

    /**
     * Get project's stories
     *
     * @param  \Dte\BtsBundle\Entity\Project $project
     * @return array
     */
    public function getProjectStories($project)
    {
        $stories = array();

        if (null !== $project) {
            $stories = $this->em->getRepository('DteBtsBundle:Issue')->findStoriesByProject($project);
        }

        return $stories;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isCreateContext = ($options['form_context'] === self::CREATE_CONTEXT);
        $isEditContext   = ($options['form_context'] === self::EDIT_CONTEXT);
        $isSubtask       = $options['isSubtask'];

        $user = $this->getUser();

        $builder->add('project', 'entity', array(
            'required'      => true,
            'label'         => 'bts.entity.issue.project.label',
            'read_only'     => $isEditContext || ($isCreateContext && $isSubtask),
            'property'      => 'selectLabel',
            'class'         => 'DteBtsBundle:Project',
            'empty_value'   => 'bts.entity.issue.project.empty_value',
            'query_builder' => function(ProjectRepository $em) use ($user) {
                return $em->findByMemberQueryBuilder($user);
            },
        ))->add('code', 'text', array(
            'required'  => false,
            'read_only' => true,
            'label'     => 'bts.entity.issue.code.label',
        ))->add('type', 'choice', array(
            'label'     => 'bts.entity.issue.type.label',
            'read_only' => $isEditContext || ($isCreateContext && $isSubtask),
            'choices'   => IssueTaskType::getItems(),
        ))->add('summary', 'text', array(
            'label'    => 'bts.entity.issue.summary.label',
            'required' => true,
        ))->add('description', 'textarea', array(
            'required' => false,
            'label'    => 'bts.entity.issue.description.label',
        ))->add('status', 'entity', array(
            'label'         => 'bts.entity.issue.status.label',
            'required'      => true,
            'read_only'     => $isCreateContext,
            'property'      => 'label',
            'class'         => 'DteBtsBundle:IssueStatus',
            'query_builder' => function(EntityRepository $em) {
                return $em->createQueryBuilder('i')->orderBy('i.order', 'ASC');
            },
        ))->add('priority', 'entity', array(
            'label'         => 'bts.entity.issue.priority.label',
            'required'      => true,
            'property'      => 'label',
            'class'         => 'DteBtsBundle:IssuePriority',
            'query_builder' => function(EntityRepository $em) {
                return $em->createQueryBuilder('i')->orderBy('i.order', 'ASC');
            },
        ))->add('resolution', 'entity', array(
            'label'         => 'bts.entity.issue.resolution.label',
            'read_only'     => $isCreateContext,
            'required'      => false,
            'property'      => 'label',
            'class'         => 'DteBtsBundle:IssueResolution',
            'empty_value'   => 'bts.entity.issue.resolution.empty_value',
            'query_builder' => function(EntityRepository $em) {
                return $em->createQueryBuilder('i')->orderBy('i.order', 'ASC');
            },
        ));

        $formModifier = function (FormEvent $event) use ($isCreateContext, $isSubtask) {
            $issue   = $event->getData();
            $members = $this->getProjectMembers($issue->getProject());
            $stories = $this->getProjectStories($issue->getProject());

            $event->getForm()->add('parent', 'entity', array(
                'label'       => 'bts.entity.issue.parent.label',
                'required'    => false,
                'read_only'   => ($issue->getType() != IssueTaskType::SUBTASK_TYPE || ($isCreateContext && $isSubtask)),
                'property'    => 'selectLabel',
                'class'       => 'DteBtsBundle:Issue',
                'empty_value' => 'bts.entity.issue.parent.empty_value',
                'choices'     => $stories,
            ))->add('assignee', 'entity', array(
                'label'       => 'bts.entity.issue.assignee.label',
                'required'    => false,
                'property'    => 'fullname',
                'class'       => 'DteBtsBundle:User',
                'empty_value' => 'bts.entity.issue.assignee.empty_value',
                'choices'     => $members,
            ));
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, $formModifier);
        $builder->addEventListener(FormEvents::SUBMIT, $formModifier);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'   => 'Dte\BtsBundle\Entity\Issue',
            'form_context' => self::CREATE_CONTEXT,
            'isSubtask'    => false,
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
