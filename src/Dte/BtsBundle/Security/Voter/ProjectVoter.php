<?php

namespace Dte\BtsBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectVoter extends AbstractRoleHierarchyVoter
{
    const VIEW    = 'view';
    const EDIT    = 'edit';

    /**
     * {@inheritDoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
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
     * @var \Dte\BtsBundle\Entity\Project $object
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

        if ($this->hasRole($token, 'ROLE_MANAGER')) {
            return VoterInterface::ACCESS_GRANTED;
        }

        switch($attribute) {
            case self::VIEW:
                foreach ($object->getMembers() as $member) {
                    if ($member->getId() === $user->getId()) {
                        return VoterInterface::ACCESS_GRANTED;
                    }
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
