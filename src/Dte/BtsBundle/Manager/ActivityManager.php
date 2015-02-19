<?php

namespace Dte\BtsBundle\Manager;

use Doctrine\ORM\EntityManager;

use Dte\BtsBundle\Entity\Activity;
use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\User;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ActivityManager
{
    /**
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Activities to save
     * @var array
     */
    private $activities = array();

    /**
     * Constructor
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * get current User
     * @var User
     */
    public function getUser()
    {
        return $this->container->get('security.context')->getToken()->getUser();
    }

    /**
     * get current EntityManager
     * @var EntityManager
     */
    public function getEntityManager()
    {
        return $this->container->get('doctrine')->getEntityManager();
    }

    /**
     * Log persist issue
     *
     * @param Issue $issue
     */
    public function logPersistIssue(Issue $issue)
    {
        $message = 'New issue added';
        $this->addActivity($message, $issue, $issue->getReporter());
    }

    /**
     * Log change status issue
     *
     * @param Issue $issue
     */
    public function logUpdateIssueStatus(Issue $issue)
    {
        $message = 'Issue status changed to ' . $issue->getStatus()->getLabel();
        $this->addActivity($message, $issue, $this->getUser());
    }

    /**
     * Log persist comment
     *
     * @param Issue $issue
     */
    public function logPersistComment(Comment $comment)
    {
        $message = 'New comment added';
        $this->addActivity($message, $comment->getIssue(), $comment->getUser());
    }

    /**
     * add an activity entity
     *
     * @param string $message
     * @param Issue $issue
     * @param User $user
     */
    public function addActivity($message, Issue $issue, User $user)
    {
        $activity = new Activity();

        $activity->setMessage($message);
        $activity->setIssue($issue);
        $activity->setUser($user);

        $this->activities[] = $activity;
    }

    /**
     *  Save activities
     */
    public function saveActivities()
    {
        if (!empty($this->activities)) {
            $em = $this->getEntityManager();

            foreach ($this->activities as $activity) {
                $em->persist($activity);
            }

            $this->activities = [];
            $em->flush();
        }
    }
}
