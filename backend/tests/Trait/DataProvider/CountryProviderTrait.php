<?php

declare(strict_types=1);

namespace App\Tests\Trait\DataProvider;

use App\Entity\Country;
use App\Repository\CountryRepository;

trait CountryProviderTrait
{
    use FormatDataProviderTrait;

    /**
     * @return array<int, array<Country>>
     */
    public static function provideCountries(): array
    {
        $countries = static::getContainer()->get(CountryRepository::class)->findAll();

        return static::formatFixtureDataForDataProvider($countries);
    }
}
