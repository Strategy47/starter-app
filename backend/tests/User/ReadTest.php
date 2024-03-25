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

class ReadTest extends ApiTestCase
{
    use CommonTrait, UserTrait, UserProviderTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    #[Test]
    #[DataProvider('provideAllUsersFromDoctrine')]
    public function userNotAuthenticatedShouldNotGetUser(User $user): void
    {
        // not authenticated user
        $this->client->request(Request::METHOD_GET, sprintf('/users/%s', $user->getId()));

        static::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    #[DataProvider('provideAllUsersFromDoctrine')]
    public function userAdminShouldGetUser(User $user): void
    {
        $this->createAuthenticatedClient(
            $this->findUserByRole(User::ROLE_ADMIN)
        );
        $this->client->request(Request::METHOD_GET, sprintf('/users/%s', $user->getId()));

        static::assertResponseIsSuccessful();
        static::assertJsonContains(
            $this->generateUserAssert($user)
        );
    }

    #[Test]
    #[DataProvider('provideUsersNotAdminFromDoctrine')]
    public function usersShouldGetCurrentUser(User $user): void
    {
        $this->createAuthenticatedClient($user);
        $this->client->request(Request::METHOD_GET, sprintf('/users/%s', $user->getId()));

        static::assertResponseIsSuccessful();
        static::assertJsonContains(
            $this->generateUserAssert($user)
        );
    }

    #[Test]
    #[DataProvider('provideUsersNotAdminFromDoctrine')]
    public function OtherUsersShouldNotGetUser(User $user): void
    {
        /** @var int $userId */
        $userId = $this->findUserByRole(User::ROLE_ADMIN)->getId();

        $this->createAuthenticatedClient($user);
        $this->client->request(Request::METHOD_GET, sprintf('/users/%s', $userId));

        static::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
