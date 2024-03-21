<?php

declare(strict_types=1);

namespace App\DataFixtures\Utils;

use App\Entity\Address;
use App\Repository\CountryRepository;
use Faker\Factory;

trait AddressTrait
{
    abstract protected function getCountryRepository(): CountryRepository;

    protected function createAddress() : Address
    {
        $countries = $this->getCountryRepository()->findAll();
        $faker = Factory::create();

        return (new Address())
            ->setAddress($faker->address)
            ->setZipCode($faker->postcode)
            ->setCity($faker->city)
            ->setCountry($faker->randomElement($countries));
    }
}
