<?php

declare(strict_types=1);

namespace App\Tests\Authentication;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Trait\CommonTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationEmailTest extends ApiTestCase
{
    use CommonTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    /** @test */
    public function userShouldAuthenticate(): void
    {
        $response = $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'email' => 'dev-admin@my-app.loc',
                'password' => 'Pass_123',
            ],
        ])->toArray();

        self::assertResponseIsSuccessful();
        self::assertArrayHasKey('token', $response);
    }

    /** @test */
    public function userShouldNotAuthenticateWithInvalidFields(): void
    {
        // Test wrong credentials
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'email' => 'test@test.fr',
                'password' => 'test@test.fr',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertJsonContains([
            'message' => $this->hydra('error.user.invalid_credentials')
        ]);
    }

    /** @test */
    public function userShouldNotAuthenticateIfInactive(): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'email' => 'dev-inactive@my-app.loc',
                'password' => 'Pass_012',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertJsonContains([
            'message' => $this->hydra('error.user.inactive')
        ]);
    }

    /** @test */
    public function userShouldNotAuthenticateIfInactiveAgency(): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'email' => 'dev-agency-inactive@my-app.loc',
                'password' => 'Pass_012',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertJsonContains([
            'message' => $this->hydra('error.agency.inactive')
        ]);
    }

    /** @test */
    public function userShouldNotAuthenticateIfPhoneNotValidate(): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'email' => 'dev-no-email@my-app.loc',
                'password' => 'Pass_012',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertJsonContains([
            'message' => $this->hydra('error.user.email_not_verified')
        ]);
    }
}
