<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class UserChecker implements UserCheckerInterface
{
    private Request $request;
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

        /** @var array<string> $requestContent */
        $requestContent = json_decode($this->request->getContent(), true);
        $identifier = array_key_exists('identifier', $requestContent) ? $requestContent['identifier'] : null;

        if (array_key_exists('identifier', $requestContent)) {
            if (!is_string($identifier)) {
                throw new BadRequestHttpException('error.key.identifier');
            }
        }

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            if (!$user->isEmailVerified()) {
                throw new CustomUserMessageAccountStatusException('error.user.email_not_verified');
            } else {
                return;
            }
        }

        if (!$user->isPhoneVerified()) {
            throw new CustomUserMessageAccountStatusException('error.user.phone_not_verified');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
