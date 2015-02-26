<?php

namespace Dte\BtsBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends AbstractRoleHierarchyVoter
{
    const CREATE  = 'create';
    const VIEW    = 'view';
    const EDIT    = 'edit';
    const PROFILE = 'profile';

    /**
     * {@inheritDoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::CREATE,
            self::VIEW,
            self::EDIT,
            self::PROFILE,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Dte\BtsBundle\Entity\User';

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

        if ($this->hasRole($token, 'ROLE_ADMIN')) {
            return VoterInterface::ACCESS_GRANTED;
        }

        switch ($attribute) {
            case self::CREATE:
                break;
            case self::VIEW:
                if (is_object($object)) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
            case self::EDIT:
            case self::PROFILE:
                if ($object->getId() === $user->getId()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
