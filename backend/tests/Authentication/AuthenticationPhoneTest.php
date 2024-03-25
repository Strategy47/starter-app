<?php

declare(strict_types=1);

namespace App\Tests\Authentication;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\UserFixtures;
use App\Tests\Trait\CommonTrait;
use App\Tests\Trait\DataProvider\UserFixturesProviderTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\Attributes\Test;

class AuthenticationPhoneTest extends ApiTestCase
{
    use CommonTrait, UserFixturesProviderTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    /**
     * @param array<mixed> $fixture
     */
    #[Test]
    #[DataProvider('provideUsersWithVerifiedPhone')]
    public function userShouldAuthenticateWithPhone(array $fixture): void
    {
        $response = $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => $fixture['phone'],
                'password' => $fixture['password'],
            ],
        ])->toArray();

        static::assertResponseIsSuccessful();
        static::assertArrayHasKey('token', $response);
    }

    #[Test]
    public function userShouldAuthenticateWithPhoneAndAgency(): void
    {
        $response = $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => '+33733333333',
                'password' => 'Pass_012',
            ],
        ])->toArray();

        static::assertResponseIsSuccessful();
        static::assertArrayHasKey('token', $response);
    }

    #[Test]
    public function userShouldNotAuthenticateWithInvalidCredentials(): void
    {
        // Test wrong credentials
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => '+33612345678',
                'password' => 'testpwd',
            ],
        ]);

        static::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        static::assertJsonContains([
            'message' => $this->hydra('error.user.invalid_credentials')
        ]);
    }

    #[Test]
    public function userShouldNotAuthenticateIfInactive(): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => '+33766666666',
                'password' => 'Pass_012',
            ],
        ]);

        static::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        static::assertJsonContains([
            'message' => $this->hydra('error.user.inactive')
        ]);
    }

    #[Test]
    public function userShouldNotAuthenticateIfInactiveAgency(): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => '+33744444444',
                'password' => 'Pass_012',
            ],
        ]);

        static::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        static::assertJsonContains([
            'message' => $this->hydra('error.agency.inactive')
        ]);
    }

    /**
     * @param array<mixed> $fixture
     */
    #[Test]
    #[DataProvider('provideUsersWithNotVerifiedPhone')]
    public function userShouldNotAuthenticateIfPhoneNotVerified(array $fixture): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => $fixture['phone'],
                'password' => $fixture['password']
            ],
        ]);

        static::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        static::assertJsonContains([
            'message' => $this->hydra('error.user.phone_not_verified')
        ]);
    }
}
