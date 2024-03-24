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

class ReadCollectionTest extends ApiTestCase
{
    use CommonTrait, UserTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    #[Test]
    public function userNotAuthenticatedShouldNotListUsers(): void
    {
        // not authenticated user
        $this->client->request(Request::METHOD_GET, '/users');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function userNotAdminShouldNotListUsers(): void
    {
        $users = $this->findUserWithoutRole(User::ROLE_ADMIN);

        foreach ($users as $user) {
            $this->createAuthenticatedClient($user);

            $this->client->request(Request::METHOD_GET, '/users');

            self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        }
    }

    #[Test]
    public function userAdminShouldListUsers(): void
    {
        $users = static::getContainer()->get(UserRepository::class)->findAll();
        $assert = [];

        foreach ($users as $user) {
            $assert[] = $this->generateUserAssert($user);
        }

        $this->createAuthenticatedClient(
            $this->findUserByRole(User::ROLE_ADMIN)
        );

        $this->client->request(Request::METHOD_GET, '/users');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'hydra:member' => array_slice($assert, 0, 30),
            'hydra:totalItems' => count($users)
        ]);
    }
}
