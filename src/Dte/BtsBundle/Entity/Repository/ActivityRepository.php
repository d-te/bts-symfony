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
            ->createQueryBuilder('a')
            ->select('a, i')
            ->leftJoin('a.issue', 'i')
            ->leftJoin('i.project', 'p')
            ->where('p.id = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('a.created', 'DESC')
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
            ->createQueryBuilder('a')
            ->select('a, i')
            ->leftJoin('a.issue', 'i')
            ->leftJoin('i.project', 'p')
            ->leftJoin('p.members', 'm')
            ->where('m.id = :user')
            ->setParameter('user', $user->getId())
            ->orderBy('a.created', 'DESC')
            ->getQuery();

        return $q->getResult();
    }
}
