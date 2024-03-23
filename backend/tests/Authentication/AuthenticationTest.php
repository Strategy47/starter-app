<?php

declare(strict_types=1);

namespace App\Tests\Authentication;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Trait\CommonTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\Attributes\Test;

class AuthenticationTest extends ApiTestCase
{
    use CommonTrait;

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

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    public function testUserShouldNotAuthenticateWithEmailWithoutPassword(): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => 'dev-admin@my-app.loc',
                'nie' => 'Pass_123',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    public function userShouldNotAuthenticateWithPhoneWithoutPassword(): void
    {
        $this->client->request(Request::METHOD_POST, '/authenticate', [
            'json' => [
                'identifier' => '0611111111',
                'nie' => 'Pass_123',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
