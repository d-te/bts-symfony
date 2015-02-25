<?php

namespace Dte\BtsBundle\Entity\Repository;

use Dte\BtsBundle\Entity\User;

use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository
{

    /**
     * Load list of projects by member
     *
     * @param  User $user
     * @return  array
     */
    public function findByMember(User $user)
    {
        return $this->findByMemberQueryBuilder($user)->getQuery()->getResult();
    }

    /**
     * Load list of projects by member
     *
     * @param  User $user
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findByMemberQueryBuilder(User $user)
    {
        $q = $this
            ->createQueryBuilder('project')
            ->select('project')
            ->leftJoin('project.members', 'member')
            ->where('member.id = :user')
            ->setParameter('user', $user->getId());

        return $q;
    }
}
