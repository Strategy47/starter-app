<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\Utils\AddressTrait;
use App\Entity\Agency;
use App\Repository\CountryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AgencyFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    use AddressTrait;

    private array $fixtures = [
        [
            'email' => 'dev-agency@my-app.loc',
            'active' => true,
        ],
        [
            'email' => 'dev-agency-inactive@my-app.loc',
            'active' => false,
        ],
    ];
    private readonly Generator $faker;

    public static function getGroups(): array
    {
        return ['dev', 'test'];
    }

    public function getDependencies(): array
    {
        return [
            CountryFixtures::class
        ];
    }

    public function __construct(private readonly CountryRepository $countryRepository) {
        $this->faker = Factory::create();
    }

    protected function getCountryRepository(): CountryRepository
    {
        return $this->countryRepository;
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->fixtures as $fixture) {
            $agency = (new Agency())
            ->setName($this->faker->company)
                ->setEmail($fixture['email'])
                ->setActive($fixture['active'])
                ->setSiret((string) $this->faker->numberBetween())
                ->setAddress($this->createAddress());

            $manager->persist($agency);
        }

        $manager->flush();
    }
}
