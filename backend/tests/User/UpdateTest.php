<?php

declare(strict_types=1);

namespace App\Tests\User;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Tests\Trait\Assert\UserTrait;
use App\Tests\Trait\CommonTrait;
use App\Tests\Trait\DataProvider\UserProviderTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateTest extends ApiTestCase
{
    use CommonTrait, UserTrait, UserProviderTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    #[Test]
    #[DataProvider('provideAllUsersFromDoctrine')]
    public function userNotAuthenticatedShouldNotUpdateUser(User $user): void
    {
        // not authenticated user
        $this->client->request(Request::METHOD_PATCH, sprintf('/users/%s', $user->getId()),
        [
            'json' => [
                'firstname' => 'test'
            ]
        ]);

        static::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    #[DataProvider('provideAllUsersFromDoctrine')]
    public function userAdminShouldUpdateUser(User $user): void
    {
        $this->createAuthenticatedClient(
            $this->findUserByRole(User::ROLE_ADMIN)
        );

        $this->client->request(Request::METHOD_PATCH, sprintf('/users/%s', $user->getId()),
            [
                'json' => [
                    'firstname' => 'firstname',
                    'lastname' => 'lastname'
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ]);

        static::assertResponseIsSuccessful();

        $assert = $this->generateUserAssert($user);
        $assert['firstname'] = 'firstname';
        $assert['lastname'] = 'lastname';

        static::assertJsonContains($assert);
    }

    #[Test]
    #[DataProvider('provideAllUsersFromDoctrine')]
    public function userAdminShouldToggleActiveUser(User $user): void
    {
        $this->createAuthenticatedClient(
            $this->findUserByRole(User::ROLE_ADMIN)
        );

        $this->client->request(Request::METHOD_PATCH, sprintf('/users/%s', $user->getId()),
            [
                'json' => [
                    'active' => !$user->isActive(),
                    'phoneVerified' => !$user->isPhoneVerified(),
                    'emailVerified' => !$user->isEmailVerified(),
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ]);

        static::assertResponseIsSuccessful();

        $assert = $this->generateUserAssert($user);
        $assert['active'] = !$user->isActive();
        $assert['phoneVerified'] = !$user->isPhoneVerified();
        $assert['emailVerified'] = !$user->isEmailVerified();

        static::assertJsonContains($assert);
    }

    #[Test]
    #[DataProvider('provideUsersNotAdminFromDoctrine')]
    public function userNotAdminShouldNotToggleActiveUser(User $user): void
    {
        $this->createAuthenticatedClient($user);

        $this->client->request(Request::METHOD_PATCH, sprintf('/users/%s', $user->getId()),
            [
                'json' => [
                    'active' => !$user->isActive(),
                    'phoneVerified' => !$user->isPhoneVerified(),
                    'emailVerified' => !$user->isEmailVerified(),
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ]);

        static::assertResponseIsSuccessful();

        $assert = $this->generateUserAssert($user);

        static::assertJsonContains($assert);
    }

    #[Test]
    #[DataProvider('provideAllValidUsersFromDoctrine')]
    public function userShouldUpdateCurrentUser(User $user): void
    {
        $this->createAuthenticatedClient($user);

        $this->client->request(Request::METHOD_PATCH, sprintf('/users/%s', $user->getId()),
            [
                'json' => [
                    'firstname' => 'firstname',
                    'lastname' => 'lastname'
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ]);

        static::assertResponseIsSuccessful();

        $assert = $this->generateUserAssert($user);
        $assert['firstname'] = 'firstname';
        $assert['lastname'] = 'lastname';

        static::assertJsonContains($assert);
    }

    public function anyBodyShouldNotUpdateUserWithNonUniqueEmail(): void
    {
        // FIXME: implement me
    }

    public function anyBodyShouldNotUpdateUserWithNonUniquePhone(): void
    {
        // FIXME: implement me
    }
}
