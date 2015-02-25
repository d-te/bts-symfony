<?php

namespace Dte\BtsBundle\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter implements VoterInterface
{
    const DELETE = 'delete';
    const EDIT   = 'edit';

    /**
     *  @var \Symfony\Component\Security\Core\Role\RoleHierarchy
     */
    protected $roleHierarchy;

    /**
     * Constructor
     * @param RoleHierarchy $roleHierarchy
     */
    public function __construct(RoleHierarchy $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * Has role
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

    /**
     * @inheritDoc
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::DELETE,
            self::EDIT,
        ));
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Dte\BtsBundle\Entity\Comment';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @inheritDoc
     * @var \Dte\BtsBundle\Entity\Comment $object
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$this->supportsClass(get_class($object))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $attribute = $attributes[0];

        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        if ($this->hasRole($token, 'ROLE_ADMIN')) {
            return VoterInterface::ACCESS_GRANTED;
        }

        switch($attribute) {
            case self::EDIT:
            case self::DELETE:
                if ($object->getUser()->getId() === $user->getId()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
