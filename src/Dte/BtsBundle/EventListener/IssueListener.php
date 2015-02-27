<?php

namespace Dte\BtsBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class IssueListener
{
    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var array
     */
    private $collaborators = [];

    /**
     * @var Boolean
     */
    private $needsFlush = false;

    /**
     * Constructor
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Get current User
     *
     * @return User
     */
    public function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }

   /**
     * {@inheritDoc}
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Issue) {
            $this->addIssueCode($entity);
            $this->addIssueReporterCollaborator($entity);
            $this->addIssueAssigneeCollaborator($entity);
        } elseif ($entity instanceof Comment) {
            $this->addCommentCollaborator($entity);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Issue) {
            $this->addIssueReporterAndAssignee($entity);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

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
        $issue->setCode($issue->generateCode());
        $this->needsFlush = true;
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
     * {@inheritDoc}
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (!empty($this->collaborators)) {
            $em = $args->getEntityManager();

            foreach ($this->collaborators as $item) {
                $item['issue']->getCollaborators();

                if (!$item['issue']->hasCollaborator($item['user'])) {
                    $item['issue']->addCollaborator($item['user']);
                }
            }

            $this->collaborators = [];
            $this->needsFlush = true;
        }

        if ($this->needsFlush) {
            $this->needsFlush = false;
            $em->flush();
        }
    }
}
