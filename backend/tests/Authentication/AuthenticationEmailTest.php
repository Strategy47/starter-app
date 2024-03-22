<?php

declare(strict_types=1);

namespace App\Tests\Authentication;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\UserFixtures;
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
    public function userShouldAuthenticateWithEmail(): void
    {
        foreach (UserFixtures::$fixtures as $fixture) {
            if ($fixture['active'] && $fixture['emailVerified'] && !isset($fixture['agency'])) {
                $response = $this->client->request(Request::METHOD_POST, '/authenticate', [
                    'json' => [
                        'identifier' => $fixture['email'],
                        'password' => $fixture['password'],
                    ],
                ])->toArray();

                self::assertResponseIsSuccessful();
                self::assertArrayHasKey('token', $response);
            }
        }
    }

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
    public function userShouldNotAuthenticateIfEmailNotValidate(): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => 'dev-no-email@my-app.loc',
                'password' => 'Pass_012',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertJsonContains([
            'message' => $this->hydra('error.user.email_not_verified')
        ]);
    }
}