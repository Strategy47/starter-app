<?php

declare(strict_types=1);

namespace App\Event\Interface;

interface DisableListenerInterface
{
    public function disable(): void;
}
