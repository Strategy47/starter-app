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

class AuthenticationEmailTest extends ApiTestCase
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
    #[DataProvider('provideUsersWithVerifiedEmail')]
    public function userShouldAuthenticateWithEmail(array $fixture): void
    {
        $response = $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => $fixture['email'],
                'password' => $fixture['password'],
            ],
        ])->toArray();

        static::assertResponseIsSuccessful();
        static::assertArrayHasKey('token', $response);
    }

    #[Test]
    public function userShouldAuthenticateWithEmailAndAgency(): void
    {
        $response = $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => 'dev-agency-active@my-app.loc',
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
                'identifier' => 'test@test.fr',
                'password' => 'test@test.fr',
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
                'identifier' => 'dev-inactive@my-app.loc',
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
                'identifier' => 'dev-agency-inactive@my-app.loc',
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
    #[DataProvider('provideUsersWithNotVerifiedEmail')]
    public function userShouldNotAuthenticateIfEmailNotVerified(array $fixture): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => $fixture['email'],
                'password' => $fixture['password'],
            ],
        ]);

        static::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        static::assertJsonContains([
            'message' => $this->hydra('error.user.email_not_verified')
        ]);
    }
}
