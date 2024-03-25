<?php

declare(strict_types=1);

namespace App\Tests\Trait\DataProvider;

use App\Entity\Locale;
use App\Repository\LocaleRepository;

trait LocaleProviderTrait
{
    use FormatDataProviderTrait;

    /**
     * @return array<int, array<Locale>>
     */
    public static function provideLocales(): array
    {
        $locales = static::getContainer()->get(LocaleRepository::class)->findAll();

        return static::formatFixtureDataForDataProvider($locales);
    }
}
