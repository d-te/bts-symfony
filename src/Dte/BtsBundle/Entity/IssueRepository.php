<?php

namespace Dte\BtsBundle\Entity;

use Doctrine\ORM\EntityRepository;

class IssueRepository extends EntityRepository
{

    /**
     * Load list of projects by member
     */
    public function findStoriesByMember(User $user)
    {
        return $this->findStoriesByMemberQueryBuilder($user)->getQuery()->getResult();
    }

    /**
     * Load list of projects by member
     */
    public function findStoriesByMemberQueryBuilder(User $user)
    {
        $q = $this
            ->createQueryBuilder('i')
            ->select('i')
            ->leftJoin('i.projects', 'p')
            ->leftJoin('p.members', 'm')
            ->where('m.id = :user')
            ->setParameter('user', $user->getId());

        return $q;
    }
}
