<?php
namespace Dte\BtsBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\Project;

class IssueSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
        );
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function index(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em     = $args->getEntityManager();

        if ($entity instanceof Issue) {
            $entity->setCode(sprintf('%s-%d', $entity->getProject()->getCode(), $entity->getId()));

            $em->flush();
        }
    }
}
