<?php

declare(strict_types=1);

namespace App\Tests\Trait\DataProvider;

use App\DataFixtures\UserFixtures;

trait UserFixturesProviderTrait
{
    use FormatDataProviderTrait;

    /**
     * @return array<int, array<mixed>>
     */
    public static function provideUsersWithVerifiedEmail(): array
    {
        $fixtures = array_filter(UserFixtures::$fixtures, fn($fixture) =>
            isset($fixture['email']) && $fixture['active'] && $fixture['emailVerified'] && !isset($fixture['agency'])
        );

        return static::formatFixtureDataForDataProvider($fixtures);
    }

    /**
     * @return array<int, array<mixed>>
     */
    public static function provideUsersWithNotVerifiedEmail(): array
    {
        $fixtures = array_filter(UserFixtures::$fixtures, fn($fixture) =>
            isset($fixture['email']) && $fixture['active'] && false === $fixture['emailVerified'] && !isset($fixture['agency'])
        );

        return static::formatFixtureDataForDataProvider($fixtures);
    }

    /**
     * @return array<int, array<mixed>>
     */
    public static function provideUsersWithVerifiedPhone(): array
    {
        $fixtures = array_filter(UserFixtures::$fixtures, fn($fixture) =>
            isset($fixture['phone']) && $fixture['active'] && $fixture['phoneVerified'] && !isset($fixture['agency'])
        );

        return static::formatFixtureDataForDataProvider($fixtures);
    }

    /**
     * @return array<int, array<mixed>>
     */
    public static function provideUsersWithNotVerifiedPhone(): array
    {
        $fixtures = array_filter(UserFixtures::$fixtures, fn($fixture) =>
            isset($fixture['phone']) && $fixture['active'] && false === $fixture['phoneVerified'] && !isset($fixture['agency'])
        );

        return static::formatFixtureDataForDataProvider($fixtures);
    }

    /**
     * @return array<int, array<mixed>>
     */
    public static function provideAllVerifiedUsers(): array
    {
        $fixtures = array_filter(UserFixtures::$fixtures, fn($fixture) =>
            $fixture['active'] && !isset($fixture['agency']) && (
                isset($fixture['phone']) && $fixture['phoneVerified'] ||
                isset($fixture['email']) && $fixture['emailVerified']
            )
        );

        return static::formatFixtureDataForDataProvider($fixtures);
    }
}
