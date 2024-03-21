<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\Utils\UserTrait;
use App\Entity\User;
use App\Event\DisableListenerEvent;
use App\Repository\AgencyRepository;
use App\Repository\CountryRepository;
use App\Repository\LocaleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    use UserTrait;

    private array $fixtures = [
        [
            'firstname' => 'David',
            'lastname' => 'Admin',
            'email' => 'dev-admin@my-app.loc',
            'roles' => [User::ROLE_ADMIN],
            'password' => 'Pass_123',
            'active' => true,
        ],
        [
            'firstname' => 'David',
            'lastname' => 'Agency',
            'email' => 'dev-agency@my-app.loc',
            'roles' => [User::ROLE_AGENCY],
            'password' => 'Pass_456',
            'active' => true,
        ],
        [
            'firstname' => 'David',
            'lastname' => 'Owner',
            'email' => 'dev-owner@my-app.loc',
            'roles' => [User::ROLE_OWNER],
            'password' => 'Pass_789',
            'active' => true,
        ],
        [
            'firstname' => 'David',
            'lastname' => 'Owner',
            'email' => 'dev-tenant@my-app.loc',
            'roles' => [User::ROLE_TENANT],
            'active' => true,
            'password' => 'Pass_012',
        ],
        [
            'firstname' => 'David',
            'lastname' => 'Disabled',
            'email' => 'dev-inactive@my-app.loc',
            'roles' => [User::ROLE_TENANT],
            'password' => 'Pass_012',
            'active' => false
        ],
        [
            'firstname' => 'David',
            'lastname' => 'Agency active',
            'email' => 'dev-agency-active@my-app.loc',
            'roles' => [User::ROLE_AGENCY],
            'password' => 'Pass_012',
            'active' => true,
            'agency' => 'dev-agency@my-app.loc'
        ],
        [
            'firstname' => 'David',
            'lastname' => 'Agency inactive',
            'email' => 'dev-agency-inactive@my-app.loc',
            'roles' => [User::ROLE_AGENCY],
            'password' => 'Pass_012',
            'active' => true,
            'agency' => 'dev-agency-inactive@my-app.loc'
        ],
    ];

    private readonly Generator $faker;

    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly LocaleRepository $localeRepository,
        private readonly AgencyRepository $agencyRepository,
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
        return ['dev', 'test'];
    }

    public function getDependencies(): array
    {
        return [
            LocaleFixtures::class,
            AgencyFixtures::class,
            CountryFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $this->dispatcher->dispatch(
            DisableListenerEvent::create(),
            DisableListenerEvent::DISABLE
        );

        foreach ($this->fixtures as $fixture) {
            $user = $this->createUser($fixture);

            if (array_key_exists('agency', $fixture)) {
                $agency = $this->agencyRepository->findOneBy(['email' => $fixture['agency']]);
                $user->setAgency($agency);
            }

            $manager->persist($user);
        }

        $manager->flush();
    }
}
