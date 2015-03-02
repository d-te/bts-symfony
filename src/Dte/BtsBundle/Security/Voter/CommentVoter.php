<?php

namespace Dte\BtsBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends AbstractRoleHierarchyVoter
{
    const CREATE = 'create';
    const EDIT   = 'edit';
    const VIEW   = 'view';
    const DELETE = 'delete';

    /**
     * {@inheritDoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::CREATE,
            self::EDIT,
            self::VIEW,
            self::DELETE,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Dte\BtsBundle\Entity\Comment';

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
        $calss = '';

        if (is_object($object)) {
            $class = get_class($object);
        } elseif (is_string($object)) {
            $class = $object;
        } elseif (is_array($object) && array_key_exists('object', $object) && array_key_exists('issue', $object)) {
            $class = $object['object'];
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

        if ($this->hasRole($token, 'ROLE_ADMIN')) {
            return VoterInterface::ACCESS_GRANTED;
        }

        switch ($attribute) {
            case self::VIEW:
            case self::CREATE:
                if (is_array($object) && $object['issue']->getProject()->hasMember($user)) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
            case self::EDIT:
            case self::DELETE:
                if (is_object($object) && $object->getUser()->isEqualTo($user)) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
