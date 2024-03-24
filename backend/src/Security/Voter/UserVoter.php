<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\User as UserEntity;
class UserVoter extends Voter
{
    public const UPDATE = 'USER_UPDATE';
    public const READ = 'USER_READ';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::UPDATE, self::READ])
            && $subject instanceof UserEntity;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserEntity || !$subject instanceof UserEntity) {
            return false;
        }

        return match ($attribute) {
            self::UPDATE,
            self::READ => in_array(UserEntity::ROLE_ADMIN, $user->getRoles()) || $user->getId() === $subject->getId() ,
            default => false
        };
    }
}
