<?php

namespace Dte\BtsBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository
{

    /**
     * Load list of projects by member
     */
    public function findByMember(User $user)
    {
        return $this->findByMemberQueryBuilder($user)->getQuery()->getResult();
    }

    /**
     * Load list of projects by member
     */
    public function findByMemberQueryBuilder(User $user)
    {
        $q = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.members', 'm')
            ->where('m.id = :user')
            ->setParameter('user', $user->getId());

        return $q;
    }
}
