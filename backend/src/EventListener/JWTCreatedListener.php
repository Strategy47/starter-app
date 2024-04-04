<?php

namespace App\EventListener;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsEventListener(event: Events::JWT_CREATED, method: 'onJWTCreated')]
class JWTCreatedListener
{
    public function __construct(
        private readonly Security $security,
        private readonly NormalizerInterface $serializer,
        private readonly IriConverterInterface $iriConverter
    ) {
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $userToArray = $this->serializer->normalize($user, null, ['groups' => 'user:read']);
        $payload     = $event->getData();

        $payload['user'] = $userToArray;
        /** @phpstan-ignore-next-line **/
        $payload['user']['@id'] = $this->iriConverter->getIriFromResource($user);

        if ($user->getLocale() && $payload['user']['locale']) {
            $payload['user']['locale']['@id'] = $this->iriConverter->getIriFromResource($user->getLocale());
        }

        if ($user->getAddress() && $payload['user']['address']) {
            $payload['user']['address']['@id'] = $this->iriConverter->getIriFromResource($user->getAddress());
        }

        $event->setData($payload);

        $header        = $event->getHeader();
        $header['cty'] = 'JWT';

        $event->setHeader($header);
    }
}
