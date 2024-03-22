<?php

declare(strict_types=1);

namespace App\Tests\Trait;

use ApiPlatform\Symfony\Bundle\Test\Client;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

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
            'base_uri' => 'https://api.my-app.local:8000'
        ];

        $this->client = static::createClient([], $clientOptions);
        $this->client->disableReboot();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        if (!$entityManager instanceof EntityManagerInterface) {
            throw new \LogicException(sprintf('%s is not a service.', EntityManagerInterface::class));
        }

        return $entityManager;
    }

    protected function getRepository(string $class): ObjectRepository
    {
        return $this->getEntityManager()->getRepository($class);
    }

    protected function hydra(string ...$messages): string
    {
        return implode(PHP_EOL, $messages);
    }

    protected function createWarningMessage(string $messages): string
    {
        return "\e[43;30m INFO | $messages \e[0m \n";
    }
}
