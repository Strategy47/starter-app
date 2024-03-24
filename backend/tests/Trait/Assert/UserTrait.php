<?php

declare(strict_types=1);

namespace App\Tests\Trait\Assert;


use App\Entity\User;
use App\Tests\Trait\CommonTrait;

trait UserTrait
{
    use CommonTrait;

    /** @return array<string, mixed> */
    public function generateUserAssert(User $user): array
    {
        return [
            '@id' => $this->getIriFromItem($user),
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'createdAt' => $user->getCreatedAt()->format(\DateTimeInterface::RFC3339),
            'active' => $user->isActive(),
            'phone' => $this->formatPhone($user->getPhone()),
            'address' => $this->formatAddress($user->getAddress()),
            'locale' => $this->getIriFromItem($user->getLocale()),
            'phoneVerified' => $user->isPhoneVerified(),
            'emailVerified' => $user->isEmailVerified()
        ];
    }
}
