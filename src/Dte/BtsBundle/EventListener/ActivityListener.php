<?php

namespace Dte\BtsBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Manager\ActivityManager;

class ActivityListener
{
    /**
     * @var ActivityManager
     */
    private $activityManager;

    /**
     * Constructor
     *
     * @param ActivityManager $activityManager
     */
    public function __construct(ActivityManager $activityManager)
    {
        $this->activityManager = $activityManager;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Issue) {
            $this->activityManager->logPersistIssue($entity);
        } elseif ($entity instanceof Comment) {
            $this->activityManager->logPersistComment($entity);
        }
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Issue) {
            if ($args->hasChangedField('status')) {
                $this->activityManager->logUpdateIssueStatus($entity);
            }
        }
    }

    /**
     * PostFlush event handler
     */
    public function postFlush()
    {
        $this->activityManager->saveActivities();
    }
}
