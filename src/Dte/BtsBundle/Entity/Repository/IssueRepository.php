<?php

namespace Dte\BtsBundle\Entity\Repository;

use Dte\BtsBundle\Entity\Project;
use Dte\BtsBundle\Entity\User;

use Doctrine\ORM\EntityRepository;

class IssueRepository extends EntityRepository
{

    /**
     * Load list of stories by project
     */
    public function findStoriesByProject(Project $project)
    {
        $q = $this
            ->createQueryBuilder('i')
            ->select('i')
            ->leftJoin('i.project', 'p')
            ->where('i.project = :project AND i.type = :type')
            ->setParameter('project', $project->getId())
            ->setParameter('type', 3)
            ->getQuery();

        return $q->getResult();
    }

    /**
     * Load list of opened issues  assigned to user
     * @param User $user
     */
    public function findOpenedIssuesAssignedToUser(User $user)
    {
        $q = $this
            ->createQueryBuilder('i')
            ->select('i')
            ->where('i.assignee = :user AND i.status <> :status')
            ->setParameter('user', $user->getId())
            ->setParameter('status', 3)
            ->orderBy('i.id', 'DESC')
            ->getQuery();

        return $q->getResult();
    }


}
