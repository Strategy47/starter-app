<?php

declare(strict_types=1);

namespace App\DataFixtures\Utils;

use App\Entity\User;
use App\Repository\CountryRepository;
use App\Repository\LocaleRepository;

trait UserTrait
{
    use AddressTrait;

    abstract protected function getLocaleRepository(): LocaleRepository;
    abstract protected function getCountryRepository(): CountryRepository;

    /**
     * @param mixed[] $fixture
     */
    private function createUser(array $fixture) : User
    {
        $locale = $this->getLocaleRepository()->findOneBy(['code' => 'fr']);

        return (new User())
            ->setFirstname($fixture['firstname'])
            ->setLastname($fixture['lastname'])
            ->setEmail($fixture['email'])
            ->setPassword($fixture['password'])
            ->setRoles($fixture['roles'])
            ->setActive(!array_key_exists('active', $fixture) || $fixture['active'])
            ->setPhone($this->faker->phoneNumber)
            ->setLocale($locale)
            ->setAddress($this->createAddress());
    }
}
