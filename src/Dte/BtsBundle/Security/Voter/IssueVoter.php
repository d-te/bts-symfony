<?php

namespace Dte\BtsBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class IssueVoter extends AbstractRoleHierarchyVoter
{
    const CREATE = 'create';
    const VIEW   = 'view';
    const EDIT   = 'edit';

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
        $supportedClass = 'Dte\BtsBundle\Entity\Issue';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * Get class from mixed $object
     *
     * @param  mixed $object
     * @return  string
     */
    private function getObjectClass($object)
    {
        $class = '';

        if (is_object($object)) {
            $class = get_class($object);
        } else {
            $class = $object;
        }

        return $class;
    }

    /**
     * {@inheritDoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $attribute = $attributes[0];

        if (!$this->supportsClass($this->getObjectClass($object)) || !$this->supportsAttribute($attribute)) {
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
                if ($this->hasRole($token, 'ROLE_OPERATOR')) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
            case self::VIEW:
                if (is_object($object)) {
                    if ($object->getProject()->hasMember($user)) {
                        return VoterInterface::ACCESS_GRANTED;
                    }
                } elseif ($this->hasRole($token, 'ROLE_OPERATOR')) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
            case self::EDIT:
                if (is_object($object)) {
                    if ($object->getProject()->hasMember($user)) {
                        return VoterInterface::ACCESS_GRANTED;
                    }
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
