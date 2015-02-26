<?php

namespace Dte\BtsBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class IssueVoter extends AbstractRoleHierarchyVoter
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
        $supportedClass = 'Dte\BtsBundle\Entity\Issue';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * {@inheritDoc}
     * @var \Dte\BtsBundle\Entity\Issue $object
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
            case self::EDIT:
            case self::VIEW:
                foreach ($object->getProject()->getMembers() as $member) {
                    if ($member->getId() === $user->getId() && $this->hasRole($token, 'ROLE_OPERATOR')) {
                        return VoterInterface::ACCESS_GRANTED;
                    }
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
