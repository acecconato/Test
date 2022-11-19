<?php

namespace App\Serializer;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use App\Entity\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class ClientContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private readonly SerializerContextBuilderInterface $decorated,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    /**
     * @param array<mixed>|null $extractedAttributes
     *
     * @return array<mixed>
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;

        if (Client::class === $resourceClass &&
            isset($context['groups']) &&
            $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $context['groups'][] = 'admin.client.read';
        }

        return $context;
    }
}
