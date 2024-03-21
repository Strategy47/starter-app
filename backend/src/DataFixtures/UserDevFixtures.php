<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\Utils\UserTrait;
use App\Entity\User;
use App\Event\DisableListenerEvent;
use App\Repository\CountryRepository;
use App\Repository\LocaleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserDevFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    use UserTrait;
    private readonly Generator $faker;

    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly LocaleRepository $localeRepository,
        private readonly CountryRepository $countryRepository
    ) {
        $this->faker = Factory::create();
    }

    protected function getLocaleRepository(): LocaleRepository
    {
        return $this->localeRepository;
    }

    protected function getCountryRepository(): CountryRepository
    {
        return $this->countryRepository;
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }

    public function getDependencies(): array
    {
        return [
            LocaleFixtures::class,
            UserFixtures::class,
            CountryFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $this->dispatcher->dispatch(
            DisableListenerEvent::create(),
            DisableListenerEvent::DISABLE
        );

        foreach ($this->getRandomFixtures() as $fixture) {
            $manager->persist($this->createUser($fixture));
        }

        $manager->flush();
    }

    private function getRandomFixtures(): array
    {
        $fixtures = [];

        for ($i = 0; $i < 100; $i++) {
            $fixtures[] = [
                'firstname' => $this->faker->firstName(),
                'lastname' => $this->faker->lastName(),
                'email' => $this->faker->email(),
                'roles' => $this->faker->randomElements(User::ROLES),
                'password' => $this->faker->password(8),
                'active' => $this->faker->boolean,
                'emailVerified' => $this->faker->boolean,
                'phoneVerified' => $this->faker->boolean,
            ];
        }

        return $fixtures;
    }
}
