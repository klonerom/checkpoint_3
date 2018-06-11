<?php
namespace AppBundle\Services;


use AppBundle\Entity\Boat;
use AppBundle\Entity\Tile;
use Doctrine\ORM\EntityManagerInterface;

class MapManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function tileExists($x, $y) : bool
    {
        //query result not null = true
        return (bool) $this->entityManager->getRepository(Tile::class)->findOneBy([
            'coordX' => $x,
            'coordY' => $y
        ]);
    }

    public function getRandomIsland()
    {
        $islands = $this->entityManager->getRepository(Tile::class)->findBy([
            'type' => 'island',
        ]);

        //One random island
        $IslandRandom = array_rand($islands);

        return $islands[$IslandRandom];
    }

    public function treasureIsland()
    {
        return $this->entityManager->getRepository(Tile::class)->findOneBy([
            'hasTreasure' => true,
        ]);
    }

    public function checkTreasure(Boat $boat)
    {
        //if treasure on these coordinates => true else false
        return (bool) $this->entityManager->getRepository(Tile::class)->findOneBy([
            'coordX' => $boat->getCoordX(),
            'coordY' => $boat->getCoordY(),
            'hasTreasure' => true,
        ]);
    }

}