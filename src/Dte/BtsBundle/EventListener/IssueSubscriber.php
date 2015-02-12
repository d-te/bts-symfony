<?php
namespace Dte\BtsBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\Project;

use Symfony\Component\DependencyInjection\ContainerInterface;

class IssueSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'postPersist',
        );
    }

    /**
     * @inheritDoc
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->addIssueCode($args);
    }

    /**
     * @inheritDoc
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->addIssueReporterAndAssignee($args);
    }

    /**
     * Generate issue code
     *
     * @param LifecycleEventArgs $args
     */
    public function addIssueCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em     = $args->getEntityManager();

        if ($entity instanceof Issue) {
            $entity->setCode($entity->generateCode());

            $em->flush();
        }
    }

    /**
     * Add issue reporter
     *
     * @param LifecycleEventArgs $args
     */
    public function addIssueReporterAndAssignee(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em     = $args->getEntityManager();

        if ($entity instanceof Issue) {
            $user = $this->container->get('security.context')->getToken()->getUser();

            $entity->setReporter($user);

            if (!$entity->getAssignee()) {
                $entity->setAssignee($user);
            }
        }
    }
}
