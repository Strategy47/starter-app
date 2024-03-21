<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    private readonly Request $request;
    public function __construct(
        RequestStack $requestStack
    )
    {
        if (!$requestStack->getCurrentRequest() instanceof Request)  {
            throw new \LogicException('no request found!');
        }

        $this->request = $requestStack->getCurrentRequest();
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->getAgency() && !$user->getAgency()->isActive()) {
            throw new CustomUserMessageAccountStatusException('error.agency.inactive');
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException('error.user.inactive');
        }

        if (!$user->isPhoneVerified() && !$user->isEmailVerified()) {
            /** @var array<string> $requestContent */
            $requestContent = json_decode($this->request->getContent(), true);

            if (array_key_exists('email', $requestContent)) {
                throw new CustomUserMessageAccountStatusException('error.user.email_not_verified');
            }

            throw new CustomUserMessageAccountStatusException('error.user.phone_not_verified');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
