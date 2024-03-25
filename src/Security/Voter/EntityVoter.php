<?php

namespace App\Security\Voter;

use App\Core\Entity\EntityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class EntityVoter extends Voter
{
    public const EDIT = 'edit';
    public const VIEW = 'show';
    public const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof EntityInterface;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return true;

                break;

            case self::VIEW:
                return true;

                break;

            case self::DELETE:
                return true;

                break;
        }

        return false;
    }
}
