<?php

declare(strict_types=1);

namespace App\Tests\Country;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Country;
use App\Entity\User;
use App\Repository\CountryRepository;
use App\Tests\Trait\CommonTrait;
use App\Tests\Trait\DataProvider\CountryProviderTrait;
use App\Tests\Trait\DataProvider\UserProviderTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;

class ReadTest extends ApiTestCase
{
    use CommonTrait, CountryProviderTrait, UserProviderTrait;

    /**
     * @var array<string, mixed>
     */
    private static ?array $assert = null;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    #[Test]
    public function userNotAuthenticatedShouldListCountries(): void
    {
        // not authenticated user
        $this->client->request(Request::METHOD_GET, '/countries');

        static::assertResponseIsSuccessful();
        static::assertJsonContains(
            static::getAssertList()
        );
    }

    #[Test]
    #[DataProvider('provideAllValidUsersFromDoctrine')]
    public function usersAuthenticatedShouldListCountries(User $user): void
    {
        $this->createAuthenticatedClient($user);

        $this->client->request(Request::METHOD_GET, '/countries');

        static::assertResponseIsSuccessful();
        static::assertJsonContains(
            static::getAssertList()
        );
    }

    #[Test]
    #[DataProvider('provideCountries')]
    public function userNotAuthenticatedShouldGetCountry(Country $country): void
    {
        // not authenticated user
        $this->client->request(Request::METHOD_GET, sprintf('/countries/%s', $country->getId()));

        static::assertResponseIsSuccessful();
        static::assertJsonContains([
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

            static::assertResponseIsSuccessful();
            static::assertJsonContains([
                'id' => $country->getId(),
                'code' => $country->getCode(),
                'name' => $country->getName()
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function getAssertList(): array
    {
        if(is_null(self::$assert)) {
            $countries = static::getContainer()->get(CountryRepository::class)->findAll();
            $assert = [];

            foreach ($countries as $country) {
                $assert[] = [
                    'id' => $country->getId(),
                    'code' => $country->getCode(),
                    'name' => $country->getName()
                ];
            }

            self::$assert = [
                'hydra:member' => array_slice($assert, 0, 30),
                'hydra:totalItems' => count($countries),
            ];
        }

        return self::$assert;
    }
}
