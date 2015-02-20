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

        if ($entity instanceof Activity) {
            $this->sendCollaboratorsNotification($entity);
        }
    }

    private function sendCollaboratorsNotification(Activity $activity)
    {
        $collaborators = $activity->getIssue()->getCollaborators();

        $noreplyEmail = $this->container->getParameter('dte_bts.noreply_email');
        $subject      = sprintf('[BTS] [%s] notification', $activity->getIssue()->getCode());
        $body         = $this
                            ->container
                            ->get('templating')
                            ->render('DteBtsBundle:Emails:notification.html.twig', array('activity' => $activity));

        foreach ($collaborators as $collaborator) {
            if ($collaborator->getId() !== $activity->getUser()->getId()) {
                $message = $this->mailer->createMessage()
                    ->setFrom($noreplyEmail)
                    ->setSubject($subject)
                    ->setTo($collaborator->getEmail())
                    ->setBody($body, 'text/html');

                $this->mailer->send($message);
            }
        }
    }
}
