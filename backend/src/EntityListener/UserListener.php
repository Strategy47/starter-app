<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\User;
use App\Event\Interface\DisableListenerInterface;
use App\Event\Trait\DisableListenerTrait;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Autoconfigure(tags: ['doctrine.orm.entity_listener'], lazy: true)]
class UserListener implements DisableListenerInterface
{
    use DisableListenerTrait;

    public function __construct(
        private readonly UserPasswordHasherInterface $encoder,
        private readonly Security $security
    ) {
    }

    public function prePersist(User $user): void
    {
        if ($user->getPassword()) {
            $user->setPassword($this->getEncodedPassword($user));
        }
    }

    public function preUpdate(User $user, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('password')) {
            $user->setPassword($this->getEncodedPassword($user));
        }

        if ($event->hasChangedField('roles')) {
            if ($this->security->getUser() && !$this->security->isGranted(User::ROLE_ADMIN)) {
                throw new AccessDeniedHttpException();
            }
        }
    }

    protected function getEncodedPassword(User $user): string
    {
        return $this->encoder->hashPassword($user, $user->getPassword());
    }
}
