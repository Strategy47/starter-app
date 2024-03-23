<?php

declare(strict_types=1);

namespace App\Tests\Country;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Country;
use App\Repository\CountryRepository;
use App\Tests\Trait\CommonTrait;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;

class ReadTest extends ApiTestCase
{
    use CommonTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    #[Test]
    public function userNotAuthenticatedShouldListCountries(): void
    {
        $countries = static::getContainer()->get(CountryRepository::class)->findAll();
        $assert = [];

        foreach ($countries as $country) {
            $assert[] = [
                'id' => $country->getId(),
                'code' => $country->getCode(),
                'name' => $country->getName()
            ];
        }

        // not authenticated user
        $this->client->request(Request::METHOD_GET, '/countries');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'hydra:member' => array_slice($assert, 0, 30),
            'hydra:totalItems' => count($countries),
        ]);

        $users = $this->findAllValidUsers();

        foreach ($users as $user) {
            $this->createAuthenticatedClient($user);

            $this->client->request(Request::METHOD_GET, '/countries');

            self::assertResponseIsSuccessful();
            self::assertJsonContains([
                'hydra:member' => array_slice($assert, 0, 30),
                'hydra:totalItems' => count($countries),
            ]);
        }
    }

    #[Test]
    public function anybodyShouldGetCountry(): void
    {
        /** @var Country $country */
        $country = static::getContainer()->get(CountryRepository::class)->findOneBy([]);

        // not authenticated user
        $this->client->request(Request::METHOD_GET, sprintf('/countries/%s', $country->getId()));

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'id' => $country->getId(),
            'code' => $country->getCode(),
            'name' => $country->getName()
        ]);

        $users = $this->findAllValidUsers();
        foreach ($users as $user) {
            $this->createAuthenticatedClient($user);
            $this->client->request(Request::METHOD_GET, sprintf('/countries/%s', $country->getId()));

            self::assertResponseIsSuccessful();
            self::assertJsonContains([
                'id' => $country->getId(),
                'code' => $country->getCode(),
                'name' => $country->getName()
            ]);
        }
    }
}
