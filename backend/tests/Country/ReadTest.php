<?php

declare(strict_types=1);

namespace App\Tests\Country;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Country;
use App\Repository\CountryRepository;
use App\Tests\Trait\CommonTrait;
use App\Tests\Trait\DataProvider\FormatDataProviderTrait;
use App\Tests\Trait\DataProvider\UserProviderTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;

class ReadTest extends ApiTestCase
{
    use CommonTrait, UserProviderTrait, FormatDataProviderTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    #[Test]
    public function userShouldListCountries(): void
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
    #[DataProvider('provideCountries')]
    public function userNotAuthenticatedShouldGetCountry(Country $country): void
    {
        // not authenticated user
        $this->client->request(Request::METHOD_GET, sprintf('/countries/%s', $country->getId()));

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'id' => $country->getId(),
            'code' => $country->getCode(),
            'name' => $country->getName()
        ]);
    }

    #[Test]
    #[DataProvider('provideCountries')]
    public function userAuthenticatedShouldGetCountry(Country $country): void
    {
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

    /**
     * @return array<int, array<Country>>
     */
    public static function provideCountries(): array
    {
        $countries = static::getContainer()->get(CountryRepository::class)->findAll();

        return self::formatFixtureDataForDataProvider($countries);
    }
}
