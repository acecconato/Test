<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Client;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    /**
     * @param array<mixed> $context
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    /**
     * @param array<mixed> $identifiers
     * @param array<mixed> $context
     */
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    /** This addWhere extension is used to:
     * - Get all resources if the user has the ROLE_ADMIN role
     * - Get all resources owned by a user
     * - Disallow a user to fetch a resource that he doesn't own.
     */
    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Client::class !== $resourceClass ||
            $this->security->isGranted('ROLE_ADMIN') ||
            null === $user = $this->security->getUser()) {
            return;
        }

        if (User::class !== $user::class) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere("$alias.owner = :current_user");
        // @phpstan-ignore-next-line
        $queryBuilder->setParameter('current_user', $user->getId());
    }
}
