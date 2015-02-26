<?php

namespace Dte\BtsBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectVoter extends AbstractRoleHierarchyVoter
{
    const CREATE  = 'create';
    const VIEW    = 'view';
    const EDIT    = 'edit';

    /**
     * {@inheritDoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::CREATE,
            self::VIEW,
            self::EDIT,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Dte\BtsBundle\Entity\Project';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * {@inheritDoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $class = (is_object($object)) ? get_class($object) : $object;

        if (!$this->supportsClass($class)) {
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

        if ($this->hasRole($token, 'ROLE_MANAGER')) {
            return VoterInterface::ACCESS_GRANTED;
        }

        switch ($attribute) {
            case self::CREATE:
            case self::EDIT:
                break;
            case self::VIEW:
                if (is_object($object)) {
                    foreach ($object->getMembers() as $member) {
                        if ($member->getId() === $user->getId()) {
                            return VoterInterface::ACCESS_GRANTED;
                        }
                    }
                } elseif ($this->hasRole($token, 'ROLE_OPERATOR')) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
