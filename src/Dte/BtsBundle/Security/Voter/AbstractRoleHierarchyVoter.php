<?php

namespace Dte\BtsBundle\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

abstract class AbstractRoleHierarchyVoter implements VoterInterface
{
    /**
     *  @var \Symfony\Component\Security\Core\Role\RoleHierarchy
     */
    protected $roleHierarchy;

    /**
     * Constructor
     *
     * @param RoleHierarchy $roleHierarchy
     */
    public function __construct(RoleHierarchy $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * Has role
     *
     * @param  TokenInterface $token
     * @param  string         $targetRole
     * @return boolean
     */
    protected function hasRole(TokenInterface $token, $targetRole)
    {
        $reachableRoles = $this->roleHierarchy->getReachableRoles($token->getRoles());
        foreach ($reachableRoles as $role) {
            if ($role->getRole() == $targetRole) {
                return true;
            }
        }
        return false;
    }
}
