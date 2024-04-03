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

    #[Test]
    #[DataProvider('provideUsersNotAdminFromDoctrine')]
    public function userShouldNotUpdateRolesCurrentUser(User $user): void
    {
        $this->createAuthenticatedClient($user);

        $this->client->request(Request::METHOD_PATCH, sprintf('/users/%s', $user->getId()),
            [
                'json' => [
                    'roles' => User::ROLES
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ]);

        static::assertResponseIsSuccessful();

        $assert = $this->generateUserAssert($user);
        $assert['roles'] = $user->getRoles();

        static::assertJsonContains($assert);
    }

    #[Test]
    #[DataProvider('provideAllValidUsersFromDoctrine')]
    public function adminShouldUpdateUsersRoles(User $user): void
    {
        $this->createAuthenticatedClient(
            $this->findUserByRole(User::ROLE_ADMIN)
        );

        $this->client->request(Request::METHOD_PATCH, sprintf('/users/%s', $user->getId()),
            [
                'json' => [
                    'roles' => User::ROLES
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ]);

        static::assertResponseIsSuccessful();

        $assert = $this->generateUserAssert($user);
        $assert['roles'] = User::ROLES;

        static::assertJsonContains($assert);
    }

    #[Test]
    #[DataProvider('provideAllValidUsersFromDoctrine')]
    public function anyBodyShouldNotUpdateUserWithNonUniqueEmail(User $user): void
    {
        $nonUniqueEmail = $user->getEmail() === 'dev-admin@my-app.loc' ? 'dev-agency@my-app.loc' : 'dev-admin@my-app.loc';

        $this->createAuthenticatedClient($user);

        $this->client->request(Request::METHOD_PATCH, sprintf('/users/%s', $user->getId()),
            [
                'json' => [
                    'email' => $nonUniqueEmail
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ]);

        static::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        static::assertJsonContains([
            'hydra:description' => $this->hydra(
                'email: error.email.unique',
            ),
        ]);
    }

    #[Test]
    #[DataProvider('provideAllValidUsersFromDoctrine')]
    public function anyBodyShouldNotUpdateUserWithNonUniquePhone(User $user): void
    {
        $nonUniquePhone = $user->getPhone()?->getNationalNumber() === '611111111' ? '+33622222222' : '+33611111111';

        $this->createAuthenticatedClient($user);

        $this->client->request(Request::METHOD_PATCH, sprintf('/users/%s', $user->getId()),
            [
                'json' => [
                    'phone' => $nonUniquePhone
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ]);

        static::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        static::assertJsonContains([
            'hydra:description' => $this->hydra(
                'phone: error.phone.unique',
            ),
        ]);
    }
}
