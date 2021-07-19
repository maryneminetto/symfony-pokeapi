<?php


namespace App\Pokedex;


use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PokemonApi
{

    private HttpClientInterface $client;

    private PokemonRepository $pokemonRepository;

    private EntityManagerInterface $em;

    public function __construct(HttpClientInterface $client, PokemonRepository $pokemonRepository, EntityManagerInterface $em)
    {
        $this->client = HttpClient::createForBaseUri('https://pokeapi.co/api/v2/');
        $this->pokemonRepository = $pokemonRepository;
        $this->em = $em;
    }

    public function getPokemons(int $offset = 0, int $limit = 50): array
    {

        // Get pokemons from https://pokeapi.co/ by offset.
        $response = $this->client->request('GET', 'pokemon', [
            'query' => [
                'offset' => $offset,
                'limit' => $limit,
            ],
        ]);

        // If the response does not have 200 for status code, throw exception.
        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Error from Pokeapi.co');
        }

        // Return data from response as PHP Array. The method read JSON data and convert it to PHP array.
        $data = $response->toArray();

        // Init pokemons array.
        $pokemons = [];

        // Parse all pokemons return by https://pokeapi.co/ for the current HTTP request.
        foreach ($data['results'] as $pokemon) {
            // Try to match pokemon's id from the URL given. If no match, throw exception.
            if (!preg_match('/([0-9]+)\/?$/', $pokemon['url'], $matches)) {
                throw new \RuntimeException('Cannot match given url for pokemon ' . $pokemon['name']);
            }

            // Get id from matches. index 0 get the full match, next indexes (1, 2, etc…) get data surround by ()
            // in the regex.
            $id = $matches[1]; //  => 25

            $response = $this->client->request('GET', 'pokemon/'.$id);

            $pokemon = $response->toArray();

            // Add pokemon data to the pokemons array.
            $pokemons = [
                'id' => $id,
                'name' => $pokemon['name'],
                'height' => $pokemon['height'],
                'weight' => $pokemon['weight'],
                'base_experience' => $pokemon['base_experience'],
                'pokedexOrder' => $pokemon['order']
            ];

            $pokemons[] = $this->convertPokeApiTopokemon($pokemon);
        }

        // Check if a next page exist.
        if ($data['next']) {
            // Try to retrieve the offset value from next URL. If no match, throw exception.
            if (!preg_match('/\?.*offset=([0-9]+)/', $data['next'], $matches)) {
                throw new \RuntimeException('Cannot match offset on next page.');
            }

            // Get next offset.
            $nextOffset = $matches[1];

            // Recurive call to getAllpokemon with the new next offset.
            $nextpokemons = $this->getpokemons($nextOffset, $limit);

            // Merge current pokemons with the next pokemons.
            $pokemons = array_merge($pokemons, $nextpokemons);
        }

        return $pokemons;

    }

    public function convertPokeApiToPokemon(array $array): Pokemon
    {
        $pokemon = $this->pokemonRepository->findOneBy([
            'pokedexOrder' => $array['order'],
        ]);

        if (null == $pokemon){
            $pokemon = new Pokemon($array['id']);
            $pokemon->setName($array['name']);
            $pokemon->setHeight($array['height']);
            $pokemon->setWeight($array['weight']);
            $pokemon->setBaseExperience($array['base_experience']);
            $pokemon->setPokedexOrder($array['order']);

            $this->em->persist($pokemon);
            $this->em->flush();
        }

        return $pokemon;
    }

}