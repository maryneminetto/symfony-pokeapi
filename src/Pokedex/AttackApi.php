<?php


namespace App\Pokedex;


use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AttackApi
{

    private HttpClientInterface $client;

    private TypeRepository $typeRepository;

    private EntityManagerInterface $em;

    public function __construct(HttpClientInterface $client, TypeRepository $typeRepository, EntityManagerInterface $em)
    {
        $this->client = HttpClient::createForBaseUri('https://pokeapi.co/api/v2/');
        $this->typeRepository = $typeRepository;
        $this->em = $em;
    }

}