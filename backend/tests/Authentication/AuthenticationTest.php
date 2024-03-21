<?php

declare(strict_types=1);

namespace App\Tests\Authentication;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Trait\CommonTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationTest extends ApiTestCase
{
    use CommonTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    /** @test */
    public function userShouldNotAuthenticateWithNoPhoneNoEmail(): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'nie' => 'dev-admin@my-app.loc',
                'password' => 'Pass_123',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /** @test */
    public function userShouldNotAuthenticateWithEmailWithoutPassword(): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'email' => 'dev-admin@my-app.loc',
                'nie' => 'Pass_123',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /** @test */
    public function userShouldNotAuthenticateWithPhoneWithoutPassword(): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'phone' => '0111111111',
                'nie' => 'Pass_123',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
