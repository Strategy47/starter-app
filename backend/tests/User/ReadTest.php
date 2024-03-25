<?php

declare(strict_types=1);

namespace App\Tests\User;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Trait\Assert\UserTrait;
use App\Tests\Trait\CommonTrait;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReadTest extends ApiTestCase
{
    use CommonTrait, UserTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    #[Test]
    public function userNotAuthenticatedShouldNotGetUser(): void
    {
        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy([]);

        // not authenticated user
        $this->client->request(Request::METHOD_GET, sprintf('/users/%s', $user->getId()));

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function userAdminShouldGetUser(): void
    {
        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy([]);

        $this->createAuthenticatedClient(
            $this->findUserByRole(User::ROLE_ADMIN)
        );
        $this->client->request(Request::METHOD_GET, sprintf('/users/%s', $user->getId()));

        self::assertResponseIsSuccessful();
        self::assertJsonContains(
            $this->generateUserAssert($user)
        );
    }

    #[Test]
    public function usersShouldGetCurrentUser(): void
    {
        $users = $this->findUserWithoutRole(User::ROLE_ADMIN);

        foreach ($users as $user) {
            $this->createAuthenticatedClient(
                $user
            );

            $this->client->request(Request::METHOD_GET, sprintf('/users/%s', $user->getId()));

            self::assertResponseIsSuccessful();
            self::assertJsonContains(
                $this->generateUserAssert($user)
            );
        }
    }

    #[Test]
    public function OtherUsersShouldNotGetUser(): void
    {
        /** @var int $userId */
        $userId = $this->findUserByRole(User::ROLE_ADMIN)->getId();

        $users = $this->findUserWithoutRole(User::ROLE_ADMIN);

        foreach ($users as $user) {
            $this->createAuthenticatedClient(
                $user
            );

            $this->client->request(Request::METHOD_GET, sprintf('/users/%s', $userId));

            self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        }
    }
}
