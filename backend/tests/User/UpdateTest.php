<?php

declare(strict_types=1);

namespace App\Tests\User;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Tests\Trait\Assert\UserTrait;
use App\Tests\Trait\CommonTrait;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateTest extends ApiTestCase
{
    use CommonTrait, UserTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    #[Test]
    public function userNotAuthenticatedShouldNotUpdateUser(): void
    {
        $user = $this->findUserByRole(User::ROLE_OWNER);

        // not authenticated user
        $this->client->request(Request::METHOD_PATCH, sprintf('/users/%s', $user->getId()),
        [
            'json' => [
                'firstname' => 'test'
            ]
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function userAdminShouldUpdateUser(): void
    {
        $user = $this->findUserByRole(User::ROLE_OWNER);

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

        self::assertResponseIsSuccessful();

        $assert = $this->generateUserAssert($user);
        $assert['firstname'] = 'firstname';
        $assert['lastname'] = 'lastname';

        self::assertJsonContains($assert);
    }

    #[Test]
    public function userAdminShouldToggleActiveUser(): void
    {
        $user = $this->findUserByRole(User::ROLE_OWNER);

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

        self::assertResponseIsSuccessful();

        $assert = $this->generateUserAssert($user);
        $assert['active'] = !$user->isActive();
        $assert['phoneVerified'] = !$user->isPhoneVerified();
        $assert['emailVerified'] = !$user->isEmailVerified();

        self::assertJsonContains($assert);
    }

    #[Test]
    public function userNotAdminShouldNotToggleActiveUser(): void
    {
        $user = $this->findUserByRole(User::ROLE_OWNER);

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

        self::assertResponseIsSuccessful();

        $assert = $this->generateUserAssert($user);

        self::assertJsonContains($assert);
    }

    #[Test]
    public function userShouldUpdateCurrentUser(): void
    {
        $users = $this->findAllValidUsers();

        foreach ($users as $user) {
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

            self::assertResponseIsSuccessful();

            $assert = $this->generateUserAssert($user);
            $assert['firstname'] = 'firstname';
            $assert['lastname'] = 'lastname';

            self::assertJsonContains($assert);
        }
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
