<?php

namespace Dte\BtsBundle\Entity\Repository;

use Dte\BtsBundle\Entity\Project;

use Doctrine\ORM\EntityRepository;

class ActivityRepository extends EntityRepository
{

    /**
     * Load list of activities by project
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
}
