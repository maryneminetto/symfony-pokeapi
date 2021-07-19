<?php

namespace App\Entity;

use App\Repository\PokemonAttackRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass=PokemonAttackRepository::class)
 */
class PokemonAttack
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Pokemon::class, inversedBy="attacks")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"attack:get"})
     */
    private $pokemon;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Attack::class, inversedBy="pokemons")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"pokemon:get"})
     */
    private $attack;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon:get", "attack:get"})
     */
    private $level;


    public function __construct(Pokemon $pokemon, Attack $attack)
    {
        $this->pokemon = $pokemon;
        $this->attack = $attack;
    }

    public function getPokemon(): ?Pokemon
    {
        return $this->pokemon;
    }

    public function getAttack(): ?Attack
    {
        return $this->attack;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }
}
