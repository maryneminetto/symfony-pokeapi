<?php


namespace App\DataProviders;

use ApiPlatform\Core\Bridge\Doctrine\Orm\CollectionDataProvider;
use App\Entity\Attack;
use App\Pokedex\AttackApi;
use Doctrine\Persistence\ManagerRegistry;

class AttackCollectionProvider
{
    private AttackApi $attackApi;

    public function __construct(AttackApi $attackApi,ManagerRegistry $managerRegistry, iterable $collectionExtensions = [])
    {
        parent::__construct($managerRegistry, $collectionExtensions);
        $this->attackApi = $attackApi;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Attack::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $this->attackApi->getAttacks();

        return parent::getCollection($resourceClass, $operationName, $context);
    }


}