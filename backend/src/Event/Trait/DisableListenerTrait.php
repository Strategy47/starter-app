<?php

declare(strict_types=1);

namespace App\Event\Trait;

trait DisableListenerTrait
{
    private bool $enabled = true;

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function isEnable(): bool
    {
        return $this->enabled;
    }
}
