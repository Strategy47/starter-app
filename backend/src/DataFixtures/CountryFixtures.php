<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Intl\Countries;

class CountryFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager): void
    {
        $countries = Countries::getNames();
        foreach ($countries as $code => $name) {
            $country = (new Country())
                ->setCode($code)
                ->setName($name);

            $manager->persist($country);
        }

        $manager->flush();
    }
}
