<?php

declare(strict_types=1);

namespace App\Tests\Trait\DataProvider;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Repository\UserRepository;

trait UserProviderTrait
{
    use FormatDataProviderTrait;

    /**
     * @return array<int, array<User>>
     */
    public static function provideUsersNotAdminFromDoctrine(): array
    {
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);

        $qb = $userRepository->createQueryBuilder('u')
            ->where('u.active = 1')
            ->andWhere('u.phoneVerified = 1 OR u.emailVerified = 1')
            ->andWhere('u.agency IS NULL')
            ->andWhere('u.roles NOT LIKE :role')
            ->setParameter('role', '%' . User::ROLE_ADMIN . '%');

        return static::formatFixtureDataForDataProvider($qb->getQuery()->getResult());
    }

    /**
     * @return array<int, array<User>>
     */
    public static function provideAllValidUsersFromDoctrine(): array
    {
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);

        $qb = $userRepository->createQueryBuilder('u')
            ->where('u.active = 1')
            ->andWhere('u.emailVerified = 1 OR u.phoneVerified = 1')
            ->leftJoin('u.agency', 'a')
            ->andWhere('u.agency IS NULL OR a.active = 1');

        return static::formatFixtureDataForDataProvider($qb->getQuery()->getResult());
    }

    /**
     * @return array<int, array<User>>
     */
    public static function provideAllUsersFromDoctrine(): array
    {
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);

        return static::formatFixtureDataForDataProvider($userRepository->findAll());
    }
}
