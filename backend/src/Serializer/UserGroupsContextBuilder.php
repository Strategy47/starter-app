<?php

declare(strict_types=1);

namespace App\Serializer;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[AsDecorator(decorates: 'api_platform.serializer.context_builder')]
class UserGroupsContextBuilder implements SerializerContextBuilderInterface
{
    public const CURRENT_USER_ADMIN_READ_GROUP = 'current:user:admin:read';

    public const CURRENT_USER_ADMIN_UPDATE_GROUP = 'current:user:admin:update';

    public const CURRENT_USER_ADMIN_POST_GROUP = 'current:user:admin:post';

    public function __construct(
      private readonly SerializerContextBuilderInterface $decorated,
      private readonly AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    /**
     * @param Request $request
     * @param bool $normalization
     * @param array<string, mixed>|null $extractedAttributes
     * @return array<string>
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        if (isset($context['groups']) &&
            $this->authorizationChecker->isGranted(User::ROLE_ADMIN)
        ) {
            $operation = $context['operation'];

            if ($operation instanceof Post) {
                $context['groups'][] = self::CURRENT_USER_ADMIN_POST_GROUP;
            }

            if ($operation instanceof Patch || $operation instanceof Put) {
                $context['groups'][] = self::CURRENT_USER_ADMIN_UPDATE_GROUP;
            }

            if ($operation instanceof Get || $operation instanceof GetCollection) {
                $context['groups'][] = self::CURRENT_USER_ADMIN_READ_GROUP;
            }
        }

        return $context;
    }
}
