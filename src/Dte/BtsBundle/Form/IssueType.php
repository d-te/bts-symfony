<?php

namespace Dte\BtsBundle\Form;

use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\IssueTaskType;
use Dte\BtsBundle\Entity\Project;

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

    /**
     * @var Symfony\Component\Security\Core\SecurityContext
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
     */
    public function __construct(SecurityContext $securityContext, Doctrine $doctrine)
    {
        $this->securityContext = $securityContext;

        $this->em = $doctrine->getManager();
    }

    /**
     * get User
     * @return Dte\BtsBundle\Entity\User
     */
    public function getUser()
    {
        return $this->securityContext->getToken()->getUser();
    }

    /**
     * get project's members
     * @param  Dte\BtsBundle\Entity\Project $project
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
     * get project's stories
     * @param  Dte\BtsBundle\Entity\Project $project
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
        $isCreateContext = ($options['form_context'] === 'create');
        $isEditContext   = ($options['form_context'] === 'edit');
        $isSubtask       = $options['isSubtask'];

        $user = $this->getUser();

        $builder->add('project', 'entity', array(
            'required'      => true,
            'label'         => 'bts.entity.issue.project.label',
            'read_only'     => $isEditContext || ($isCreateContext && $isSubtask),
            'property'      => 'selectLabel',
            'class'         => 'DteBtsBundle:Project',
            'empty_value'   => 'bts.entity.issue.project.empty_value',
            'query_builder' => function(EntityRepository $em) use ($user) {
                return $em->findByMemberQueryBuilder($user);
            },
        ));

        $formModifier = function ($form, $issue, $project) use ($isCreateContext, $isEditContext, $isSubtask) {
            $members = $this->getProjectMembers($project);
            $stories = $this->getProjectStories($project);

            $form->add('code', 'text', array(
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
            ))->add('parent', 'entity', array(
                'label'       => 'bts.entity.issue.parent.label',
                'required'    => false,
                'read_only'   => ($issue->getType() !== 4 || ($isCreateContext && $isSubtask)),
                'property'    => 'selectLabel',
                'class'       => 'DteBtsBundle:Issue',
                'empty_value' => 'bts.entity.issue.parent.empty_value',
                'choices'     => $stories,
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
            ))->add('assignee', 'entity', array(
                'label'       => 'bts.entity.issue.assignee.label',
                'required'    => false,
                'property'    => 'fullname',
                'class'       => 'DteBtsBundle:User',
                'empty_value' => 'bts.entity.issue.assignee.empty_value',
                'choices'     => $members,
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
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm(), $event->getData(), $event->getData()->getProject());
            }
        );

        $builder->get('project')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $project   = $event->getForm()->getData();
                $issue     = $event->getForm()->getParent()->getData();
                $formModifier($event->getForm()->getParent(), $issue, $project);
            }
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'   => 'Dte\BtsBundle\Entity\Issue',
            'form_context' => 'default',
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
