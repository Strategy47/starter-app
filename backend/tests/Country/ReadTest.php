<?php
declare(strict_types=1);

namespace App\Tests\Country;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Country;
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
    public function userNotAuthenticatedShouldListCountries(): void
    {
        $countries = $this->getRepository(Country::class)->findAll();
        $assert = [];

        foreach ($countries as $country) {
            $currentCountry = [
                'id' => $country->getId(),
                'code' => $country->getCode(),
                'name' => $country->getName()
            ];

            $assert[] = $currentCountry;
        }

        $this->client->request(Request::METHOD_GET, '/countries');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'hydra:member' => array_slice($assert, 0, 30),
            'hydra:totalItems' => count($countries),
        ]);
    }

    /**
     * @test
     */
    public function userNotAuthenticatedShouldGetCountry(): void
    {
        $country = $this->getRepository(Country::class)->findOneBy([]);

        $this->client->request(Request::METHOD_GET, sprintf('/countries/%s', $country->getId()));

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'id' => $country->getId(),
            'code' => $country->getCode(),
            'name' => $country->getName()
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
