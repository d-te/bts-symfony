<?php
namespace Dte\BtsBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

use Dte\BtsBundle\Entity\Activity;
use Dte\BtsBundle\Entity\Issue;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NotificationSubscriber implements EventSubscriber
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container  = $container;
        $this->mailer     = $this->container->get('mailer');
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
        );
    }

    /**
     * @inheritDoc
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em     = $args->getEntityManager();

        if ($entity instanceof Activity) {
            $this->sendCollaboratorsNotification($entity);
        }
    }

    private function sendCollaboratorsNotification(Activity $activity)
    {
        $collaborators = $activity->getIssue()->getCollaborators();

        //$noreplyEmail = $this->container->get('dte_bts.notification.noreply_email'); //TODO temporary
        $noreplyEmail = 'noreply@dte-bts.dev';
        $subject      = sprintf('[BTS] %s updated', $activity->getIssue()->getCode());
        $body         = $this->container->get('templating')->render('DteBtsBundle:Emails:notification.html.twig', array('activity' => $activity));

        foreach ($collaborators as $collaborator) {
            if ($collaborator->getId() !== $activity->getUser()->getId()) {
                $message = $this->mailer->createMessage()
                    ->setFrom($noreplyEmail)
                    ->setSubject($subject)
                    ->setTo($collaborator->getEmail())
                    ->setBody($body);

                $this->mailer->send($message);
            }
        }
    }
}
