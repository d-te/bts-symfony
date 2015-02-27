<?php

namespace Dte\BtsBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Doctrine\ORM\EntityManager;

use Dte\BtsBundle\Entity\Activity;
use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\User;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\RouterInterface;

class ActivityManager
{
    /**
     *  @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     *  @var string
     */
    private $noreplyEmail;

    /**
     * Activities to save
     *
     * @var array
     */
    private $activities = array();

    /**
     * Constructor
     *
     * @param TokenStorageInterface $tokenStorage
     * @param Doctrine              $doctrine
     * @param \Swift_Mailer         $mailer
     * @param TranslatorInterface   $translator
     * @param RouterInterface       $router
     * @param string                $noreplyEmail
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        Doctrine $doctrine,
        \Swift_Mailer $mailer,
        TranslatorInterface $translator,
        RouterInterface $router,
        $noreplyEmail
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->doctrine     = $doctrine;
        $this->mailer       = $mailer;
        $this->translator   = $translator;
        $this->router       = $router;
        $this->noreplyEmail = $noreplyEmail;
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
     * Get current EntityManager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->doctrine->getManager();
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
     * @param Comment $comment
     */
    public function logPersistComment(Comment $comment)
    {
        $message = 'New comment added';
        $this->addActivity($message, $comment->getIssue(), $comment->getUser());
    }

    /**
     * Add an activity entity
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
        $em = $this->getEntityManager();

        if (!empty($this->activities)) {
            foreach ($this->activities as $activity) {
                $em->persist($activity);

                $this->sendCollaboratorsNotification($activity);
            }

            $this->activities = [];
            $em->flush();
        }
    }

    /**
     *  Send notification email to issue's collaborators
     *
     *  @param Activity $activity
     */
    public function sendCollaboratorsNotification(Activity $activity)
    {
        $collaborators = $activity->getIssue()->getCollaborators();
        $subject       = sprintf('[BTS] [%s] notification', $activity->getIssue()->getCode());
        $body          = $this->translator->trans(
            'bts.email.notification',
            array(
                '%issue_url%'  => $this
                    ->router
                    ->generate('dte_bts_issue_show', array('id' => $activity->getIssue()->getId()), true),
                '%issue_code%' => $activity->getIssue()->getCode(),
                '%user_url%'   => $this
                    ->router
                    ->generate('dte_bts_user_show', array('id' => $activity->getUser()->getId()), true),
                '%user_name%'  => $activity->getUser()->getFullname(),
                '%message%'    => $activity->getMessage(),
            )
        );

        foreach ($collaborators as $collaborator) {
            if (!$collaborator->isEqualTo($activity->getUser())) {
                $message = $this->mailer->createMessage()
                    ->setFrom($this->noreplyEmail)
                    ->setSubject($subject)
                    ->setTo($collaborator->getEmail())
                    ->setBody($body, 'text/html');

                $this->mailer->send($message);
            }
        }
    }
}
