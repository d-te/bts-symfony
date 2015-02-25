<?php

namespace Dte\BtsBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Manager\ActivityManager;

class ActivitySubscriber implements EventSubscriber
{
    /**
     * @var ActivityManager
     */
    private $activityManager;

    /**
     * Constructor
     */
    public function __construct(ActivityManager $activityManager)
    {
        $this->activityManager = $activityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'preUpdate',
            'postFlush',
        );
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Issue) {
            if ($args->hasChangedField('status')) {
                $this->activityManager->logUpdateIssueStatus($entity);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        $this->activityManager->saveActivities();
    }
}
