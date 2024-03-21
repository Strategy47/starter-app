<?php

declare(strict_types=1);

namespace App\Tests\Trait;

use ApiPlatform\Symfony\Bundle\Test\Client;

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

    protected function hydra(string ...$messages): string
    {
        return implode(PHP_EOL, $messages);
    }

    protected function createWarningMessage(string $messages): string
    {
        return "\e[43;30m INFO | $messages \e[0m \n";
    }
}
