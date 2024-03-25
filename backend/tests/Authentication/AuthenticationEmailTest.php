<?php

declare(strict_types=1);

namespace App\Tests\Authentication;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Trait\CommonTrait;
use App\Tests\Trait\DataProvider\UserProviderTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\Attributes\Test;

class AuthenticationEmailTest extends ApiTestCase
{
    use CommonTrait, UserProviderTrait;

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

        self::assertResponseIsSuccessful();
        self::assertArrayHasKey('token', $response);
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

        self::assertResponseIsSuccessful();
        self::assertArrayHasKey('token', $response);
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

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertJsonContains([
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

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertJsonContains([
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

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertJsonContains([
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

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertJsonContains([
            'message' => $this->hydra('error.user.email_not_verified')
        ]);
    }
}
