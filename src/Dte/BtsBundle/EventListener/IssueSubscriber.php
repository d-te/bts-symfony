<?php

namespace Dte\BtsBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;

use Symfony\Component\DependencyInjection\ContainerInterface;

class IssueSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $collaborators = [];

    /**
     * Constructor
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * get current User
     * @return User
     */
    public function getUser()
    {
        return $this->container->get('security.context')->getToken()->getUser();
    }

    /**
     * get current EntityManager
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->container->get('doctrine')->getManager();
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'postPersist',
            'preUpdate',
            'postFlush',
        );
    }

    /**
     * @inheritDoc
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em     = $args->getEntityManager();

        if ($entity instanceof Issue) {
            $this->addIssueCode($entity);
            $this->addIssueReporterCollaborator($entity);
            $this->addIssueAssigneeCollaborator($entity);
        } elseif ($entity instanceof Comment) {
            $this->addCommentCollaborator($entity);
        }
    }

    /**
     * @inheritDoc
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em     = $args->getEntityManager();

        if ($entity instanceof Issue) {
            $this->addIssueReporterAndAssignee($entity);
        }
    }

    /**
     * @inheritDoc
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em     = $args->getEntityManager();

        if ($entity instanceof Issue) {
            if ($args->hasChangedField('assignee')) {
                $this->addIssueAssigneeCollaborator($entity);
            }
        }
    }

    /**
     * Add collaborator from reporter field
     *
     * @param Issue $issue
     */
    public function addIssueReporterCollaborator(Issue $issue)
    {
        $this->collaborators[] = array('issue' => $issue, 'user' => $issue->getReporter());
    }

    /**
     * Add collaborator from assignee field
     *
     * @param Issue $issue
     */
    public function addIssueAssigneeCollaborator(Issue $issue)
    {
        if (null !== $issue->getAssignee() && $issue->getAssignee()->getId() !== $issue->getReporter()->getId()) {
            $this->collaborators[] = array('issue' => $issue, 'user' => $issue->getAssignee());
        }
    }

    /**
     * Add collaborator by Comment
     *
     * @param Comment $comment
     */
    public function addCommentCollaborator(Comment $comment)
    {
        $this->collaborators[] = array('issue' => $comment->getIssue(), 'user' => $comment->getUser());
    }

    /**
     * Generate issue code
     *
     * @param Issue $issue
     */
    public function addIssueCode(Issue $issue)
    {
        $em = $this->getEntityManager();
        $issue->setCode($issue->generateCode());
        $em->flush();
    }

    /**
     * Add issue reporter
     *
     * @param Issue $issue
     */
    public function addIssueReporterAndAssignee(Issue $issue)
    {
        if (null === $issue->getReporter()) {
            $issue->setReporter($this->getUser());
        }

        if (null === $issue->getAssignee()) {
            $issue->setAssignee($this->getUser());
        }
    }

    /**
     * @inheritDoc
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (!empty($this->collaborators)) {
            $em = $this->getEntityManager();

            foreach ($this->collaborators as $item) {
                $item['issue']->getCollaborators();

                if (!$item['issue']->hasCollaborator($item['user'])) {
                    $item['issue']->addCollaborator($item['user']);
                }
            }

            $this->collaborators = [];
            $em->flush();
        }
    }
}
