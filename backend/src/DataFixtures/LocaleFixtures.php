<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Locale;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class LocaleFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager): void
    {
        $locales = ['fr', 'en'];

        foreach ($locales as $localeCode) {
            $locale = (new Locale())
                ->setCode($localeCode)
                ->setName($localeCode);
            $manager->persist($locale);
        }

        $manager->flush();
    }
}
