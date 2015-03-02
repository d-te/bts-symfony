<?php

namespace Dte\BtsBundle\Entity\Repository;

use Dte\BtsBundle\Entity\IssueStatus;
use Dte\BtsBundle\Entity\IssueTaskType;
use Dte\BtsBundle\Entity\Project;
use Dte\BtsBundle\Entity\User;

use Doctrine\ORM\EntityRepository;

class IssueRepository extends EntityRepository
{
    /**
     * Load list of stories by project
     *
     * @param  Project $project
     * @return  array
     */
    public function findStoriesByProject(Project $project)
    {
        $q = $this
            ->createQueryBuilder('issue')
            ->select('issue')
            ->leftJoin('issue.project', 'project')
            ->where('issue.project = :project AND issue.type = :type')
            ->setParameter('project', $project->getId())
            ->setParameter('type', IssueTaskType::STORY_TYPE)
            ->getQuery();

        return $q->getResult();
    }

    /**
     * Load list of opened issues  assigned to user
     *
     * @param User $user
     * @return  array
     */
    public function findOpenedIssuesAssignedToUser(User $user)
    {
        $q = $this
            ->createQueryBuilder('issue')
            ->select('issue')
            ->leftJoin('issue.status', 'status')
            ->where('issue.assignee = :user AND status.label <> :label')
            ->setParameter('user', $user->getId())
            ->setParameter('label', IssueStatus::CLOSED_STATUS_LABEL)
            ->orderBy('issue.id', 'DESC')
            ->getQuery();

        return $q->getResult();
    }

    /**
     * Load list of opened issues where user is collaborator
     *
     * @param User $user
     * @return  array
     */
    public function findOpenedIssuesByCollaborator(User $user)
    {
        $q = $this
            ->createQueryBuilder('issue')
            ->select('issue')
            ->leftJoin('issue.collaborators', 'collaborator')
            ->where('collaborator.id = :user AND issue.status <> :status')
            ->setParameter('user', $user->getId())
            ->setParameter('status', IssueStatus::CLOSED_STATUS_LABEL)
            ->orderBy('issue.id', 'DESC')
            ->getQuery();

        return $q->getResult();
    }
}
