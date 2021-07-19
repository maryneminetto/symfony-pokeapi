<?php


namespace App\DataProviders;


use ApiPlatform\Core\Bridge\Doctrine\Orm\CollectionDataProvider;
use App\Entity\Pokemon;
use App\Pokedex\PokemonApi;
use Doctrine\Persistence\ManagerRegistry;

class PokemonCollectionProvider extends CollectionDataProvider
{

    private PokemonApi $pokemonApi;

    public function __construct(PokemonApi $pokemonApi,ManagerRegistry $managerRegistry, iterable $collectionExtensions = [])
    {
        parent::__construct($managerRegistry, $collectionExtensions);
        $this->pokemonApi = $pokemonApi;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Pokemon::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $this->pokemonApi->getPokemons();

        return parent::getCollection($resourceClass, $operationName, $context);
    }

}