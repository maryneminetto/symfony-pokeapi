<?php


namespace App\DataProviders;


use ApiPlatform\Core\Bridge\Doctrine\Orm\CollectionDataProvider;
use App\Pokedex\TypeApi;
use App\Entity\Type;
use Doctrine\Persistence\ManagerRegistry;

class TypeCollectionProvider extends CollectionDataProvider
{
    private TypeApi $typeApi;

    public function __construct(TypeApi $typeApi,ManagerRegistry $managerRegistry, iterable $collectionExtensions = [])
    {
        parent::__construct($managerRegistry, $collectionExtensions);
        $this->typeApi = $typeApi;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Type::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $this->typeApi->getTypes();

        return parent::getCollection($resourceClass, $operationName, $context);
    }


}