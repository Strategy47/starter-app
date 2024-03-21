<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class DisableListenerEvent extends Event
{
    public const DISABLE = 'app.disable_listener';

    public static function create(): DisableListenerEvent
    {
        return new self();
    }
}
