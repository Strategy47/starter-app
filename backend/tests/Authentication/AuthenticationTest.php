<?php

declare(strict_types=1);

namespace App\Tests\Authentication;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Trait\CommonTrait;
use App\Tests\Trait\DataProvider\UserFixturesProviderTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\Attributes\Test;

class AuthenticationTest extends ApiTestCase
{
    use CommonTrait, UserFixturesProviderTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    #[Test]
    public function userShouldNotAuthenticateWithoutPhoneAndEmail(): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'nie' => 'dev-admin@my-app.loc',
                'password' => 'Pass_123',
            ],
        ]);

        static::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param array<mixed> $fixture
     */
    #[Test]
    #[DataProvider('provideUsersWithVerifiedEmail')]
    public function testUserShouldNotAuthenticateWithEmailWithoutPassword(array $fixture): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => $fixture['email'],
                'nie' => 'Pass_123',
            ],
        ]);

        static::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param array<mixed> $fixture
     */
    #[Test]
    #[DataProvider('provideUsersWithVerifiedPhone')]
    public function userShouldNotAuthenticateWithPhoneWithoutPassword(array $fixture): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => $fixture['phone'],
                'nie' => 'Pass_123',
            ],
        ]);

        static::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
