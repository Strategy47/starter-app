<?php

declare(strict_types=1);

namespace App\Tests\Locale;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Locale;
use App\Repository\LocaleRepository;
use App\Tests\Trait\CommonTrait;
use App\Tests\Trait\DataProvider\FormatDataProviderTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;

class ReadTest extends ApiTestCase
{
    use CommonTrait, FormatDataProviderTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    #[Test]
    public function anyBodyShouldListLocales(): void
    {
        $locales = static::getContainer()->get(LocaleRepository::class)->findAll();
        $assert = [];

        foreach ($locales as $locale) {
            $assert[] = [
                'id' => $locale->getId(),
                'code' => $locale->getCode(),
                'name' => $locale->getName()
            ];
        }

        // not authenticated user
        $this->client->request(Request::METHOD_GET, '/locales');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'hydra:member' => array_slice($assert, 0, 30),
            'hydra:totalItems' => count($locales),
        ]);

        $users = $this->findAllValidUsers();

        foreach ($users as $user) {
            $this->createAuthenticatedClient($user);

            $this->client->request(Request::METHOD_GET, '/locales');

            self::assertResponseIsSuccessful();
            self::assertJsonContains([
                'hydra:member' => array_slice($assert, 0, 30),
                'hydra:totalItems' => count($locales),
            ]);
        }
    }

    #[Test]
    #[DataProvider('provideLocales')]
    public function userNotAuthenticatedShouldGetLocale(Locale $locale): void
    {
        // not authenticated user
        $this->client->request(Request::METHOD_GET, sprintf('/locales/%s', $locale->getId()));

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'id' => $locale->getId(),
            'code' => $locale->getCode(),
            'name' => $locale->getName()
        ]);
    }

    #[Test]
    #[DataProvider('provideLocales')]
    public function userAuthenticatedShouldGetLocale(Locale $locale): void
    {
        $users = $this->findAllValidUsers();

        foreach ($users as $user) {
            $this->createAuthenticatedClient($user);
            $this->client->request(Request::METHOD_GET, sprintf('/locales/%s', $locale->getId()));

            self::assertResponseIsSuccessful();
            self::assertJsonContains([
                'id' => $locale->getId(),
                'code' => $locale->getCode(),
                'name' => $locale->getName()
            ]);
        }
    }

    /**
     * @return array<int, array<Locale>>
     */
    public static function provideLocales(): array
    {
        $locales = static::getContainer()->get(LocaleRepository::class)->findAll();

        return self::formatFixtureDataForDataProvider($locales);
    }
}
