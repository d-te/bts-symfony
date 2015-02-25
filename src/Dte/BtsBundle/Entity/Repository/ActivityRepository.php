<?php

namespace Dte\BtsBundle\Entity\Repository;

use Dte\BtsBundle\Entity\Project;
use Dte\BtsBundle\Entity\User;

use Doctrine\ORM\EntityRepository;

class ActivityRepository extends EntityRepository
{
    /**
     * Load list of activities by project
     *
     * @param  Project $project
     * @return  array
     */
    public function findActivitiesByProject(Project $project)
    {
        $q = $this
            ->createQueryBuilder('activity')
            ->select('activity, issue')
            ->leftJoin('activity.issue', 'issue')
            ->leftJoin('i.project', 'project')
            ->where('project.id = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('activity.created', 'DESC')
            ->getQuery();

        return $q->getResult();
    }

    /**
     * Load list of activities by user
     *
     * @param  User $user
     * @return  array
     */
    public function findActivitiesByUser(User $user)
    {
        $q = $this
            ->createQueryBuilder('activity')
            ->select('activity, issue')
            ->leftJoin('activity.issue', 'issue')
            ->leftJoin('i.project', 'project')
            ->leftJoin('p.members', 'member')
            ->where('member.id = :user')
            ->setParameter('user', $user->getId())
            ->orderBy('activity.created', 'DESC')
            ->getQuery();

        return $q->getResult();
    }
}
