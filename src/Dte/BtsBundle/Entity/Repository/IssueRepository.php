<?php

namespace Dte\BtsBundle\Entity\Repository;

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
            ->setParameter('type', 3)
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
            ->where('issue.assignee = :user AND issue.status <> :status')
            ->setParameter('user', $user->getId())
            ->setParameter('status', 3)
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
            ->setParameter('status', 3)
            ->orderBy('issue.id', 'DESC')
            ->getQuery();

        return $q->getResult();
    }
}
