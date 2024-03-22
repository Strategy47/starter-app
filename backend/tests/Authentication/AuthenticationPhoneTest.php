<?php

declare(strict_types=1);

namespace App\Tests\Authentication;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\UserFixtures;
use App\Tests\Trait\CommonTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationPhoneTest extends ApiTestCase
{
    use CommonTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    /** @test */
    public function userShouldAuthenticateWithPhone(): void
    {
        foreach (UserFixtures::$fixtures as $fixture) {

            if ($fixture['active'] && $fixture['phoneVerified'] && isset($fixture['phone']) && !isset($fixture['agency'])) {
                $response = $this->client->request(Request::METHOD_POST, '/authenticate', [
                    'json' => [
                        'identifier' => $fixture['phone'],
                        'password' => $fixture['password'],
                    ],
                ])->toArray();

                self::assertResponseIsSuccessful();
                self::assertArrayHasKey('token', $response);
            }
        }
    }

    /** @test */
    public function userShouldAuthenticateWithPhoneAndAgency(): void
    {
        $response = $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => '+33733333333',
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
                'identifier' => '+33612345678',
                'password' => 'testpwd',
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
                'identifier' => '+33766666666',
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
                'identifier' => '+33744444444',
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
                'identifier' => '+33555555555',
                'password' => 'Pass_012',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertJsonContains([
            'message' => $this->hydra('error.user.phone_not_verified')
        ]);
    }
}
