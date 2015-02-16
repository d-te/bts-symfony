<?php

namespace Dte\BtsBundle\Entity\Repository;

use Dte\BtsBundle\Entity\Project;

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
}
