<?php

declare(strict_types=1);

namespace App\Tests\Registration;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Trait\Assert\UserTrait;
use App\Tests\Trait\CommonTrait;
use App\Tests\Trait\DataProvider\UserProviderTrait;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationTest extends ApiTestCase
{
    use CommonTrait, UserTrait, UserProviderTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    #[Test]
    public function userShouldNotRegisterWithInvalidFields(): void
    {
        // not authenticated user
        $this->client->request(Request::METHOD_POST,'/register', [
            'json' => [],
        ]);

        static::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        static::assertJsonContains([
            'hydra:description' => $this->hydra(
                'email: error.field.not_blank',
                'password: error.field.not_blank',
                'firstname: error.field.not_blank',
                'lastname: error.field.not_blank',
                'address: error.field.not_null',
                'locale: error.field.not_null',
            ),
        ]);
    }
}
