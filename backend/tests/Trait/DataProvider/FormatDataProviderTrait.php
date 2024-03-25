<?php

declare(strict_types=1);

namespace App\Tests\Trait\DataProvider;

use App\DataFixtures\UserFixtures;
use PHPUnit\Framework\Attributes\DataProvider;

trait FormatDataProviderTrait
{
    /**
     * @param  array<int, mixed> $verifiedUsers
     * @return array<int, array<mixed>>
     */
    protected static function formatFixtureDataForDataProvider(array $verifiedUsers): array
    {
        $data = [];
        foreach ($verifiedUsers as $fixture) {
            $data[] = [$fixture];
        }

        return $data;
    }
}
