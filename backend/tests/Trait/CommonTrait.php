<?php

declare(strict_types=1);

namespace App\Tests\Trait;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Address;
use App\Entity\Locale;
use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use libphonenumber\PhoneNumber;

trait CommonTrait
{
    protected Client $client;

    public function setUp(): void
    {
        $this->setUpClient();
    }

    protected function setUpClient(): void
    {
        $clientOptions = [
            'base_uri' => 'https://api.my-app.local:8000',
            'headers' => [
                'Content-Type' => 'application/ld+json; charset=utf-8',
                'Accept' => 'application/ld+json',
            ],
        ];

        $this->client = static::createClient([], $clientOptions);
        $this->client->disableReboot();
    }

    protected function createAuthenticatedClient(User $user): void
    {
        $jwtTokenManager = static::getContainer()->get(JWTTokenManagerInterface::class);

        $clientOptions = [
            'base_uri' => 'https://api.my-app.local:8000',
            'auth_bearer' => $jwtTokenManager->create($user),
            'headers' => [
                'Content-Type' => 'application/ld+json; charset=utf-8',
                'Accept' => 'application/ld+json',
            ],
        ];

        $this->client = static::createClient([], $clientOptions);
        $this->client->disableReboot();
    }

    protected function findUserByRole(string $role): User
    {
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);

        $qb = $userRepository->createQueryBuilder('u')
            ->where('u.active = 1')
            ->andWhere('u.emailVerified = 1')
            ->andWhere('u.agency IS NULL')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', "%$role%");

        $user = $qb->getQuery()->getOneOrNullResult();

        if (!$user instanceof User) {
            throw new \LogicException('user not found');
        }

        return $user;
    }

    /**
     * @return User[]
     */
    protected function findAllValidUsers(): array
    {
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);

        $qb = $userRepository->createQueryBuilder('u')
            ->where('u.active = 1')
            ->andWhere('u.emailVerified = 1 OR u.phoneVerified = 1')
            ->leftJoin('u.agency', 'a')
            ->andWhere('u.agency IS NULL OR a.active = 1')
        ;

        return $qb->getQuery()->getResult();
    }

    protected function hydra(string ...$messages): string
    {
        return implode(PHP_EOL, $messages);
    }

    protected function createWarningMessage(string $messages): string
    {
        return "\e[43;30m INFO | $messages \e[0m \n";
    }

    protected function getIriFromItem(?object $item): ?string
    {
        if (!$item) {
            return null;
        }

        $iri = static::getContainer()->get('api_platform.iri_converter')->getIriFromResource($item);

        if (! is_string($iri)) {
            return null;
        }

        return str_replace('//api.my-app.local', '', $iri);
    }

    protected function formatPhone(mixed $phone): ?string
    {
        if ($phone instanceof PhoneNumber) {
            return sprintf('+%s%s',$phone->getCountryCode(), $phone->getNationalNumber());
        }

        return $phone;
    }

    /**
     * @return array<string, string|null>
     */
    protected function formatAddress(?Address $address): ?array
    {
        if ($address) {
            return [
                'address' => $address->getAddress(),
                'zipCode' => $address->getZipCode(),
                'city' => $address->getCity(),
                'country' => $this->getIriFromItem($address->getCountry())

            ];
        }

        return null;
    }

    /**
     * @return array<string, int|string|null>
     */
    protected function formatLocale(?Locale $locale): ?array
    {
        if ($locale) {
            return [
                '@id' => $this->getIriFromItem($locale),
                'id' => $locale->getId(),
                'code' => $locale->getCode(),
                'name' => $locale->getName()
            ];
        }

        return null;
    }
}
