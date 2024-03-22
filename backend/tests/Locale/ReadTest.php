<?php
declare(strict_types=1);

namespace App\Tests\Locale;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Locale;
use App\Tests\Trait\CommonTrait;
use Symfony\Component\HttpFoundation\Request;

class ReadTest extends ApiTestCase
{
    use CommonTrait;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    /**
     * @test
     */
    public function userNotAuthenticatedShouldListLocales(): void
    {
        $locales = $this->getRepository(Locale::class)->findAll();
        $assert = [];

        foreach ($locales as $locale) {
            $currentCountry = [
                'id' => $locale->getId(),
                'code' => $locale->getCode(),
                'name' => $locale->getName()
            ];

            $assert[] = $currentCountry;
        }

        $this->client->request(Request::METHOD_GET, '/locales');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'hydra:member' => array_slice($assert, 0, 30),
            'hydra:totalItems' => count($locales),
        ]);
    }

    /**
     * @test
     */
    public function userNotAuthenticatedShouldGetLocale(): void
    {
        $locale = $this->getRepository(Locale::class)->findOneBy([]);

        $this->client->request(Request::METHOD_GET, sprintf('/locales/%s', $locale->getId()));

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'id' => $locale->getId(),
            'code' => $locale->getCode(),
            'name' => $locale->getName()
        ]);
    }

    /**
     * @test
     */
    public function anybodyAuthenticatedShouldListCountries(): void
    {
        // Fixme: implement me
        self::assertTrue(true);
    }

    /**
     * @test
     */
    public function anybodyAuthenticatedShouldGetCountry(): void
    {
        // Fixme: implement me
        self::assertTrue(true);
    }
}
