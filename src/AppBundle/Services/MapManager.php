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


    public function tileExists($x, $y): bool
    {
        $position = $this->entityManager->getRepository(Tile::class)->findOneBy([
            'coordX' => $x,
             'coordY' => $y
        ]);

        if ($position) {

            return true;
        }
        return false;
    }

    public function getRandomIsland()
    {
        $islands = $this->entityManager->getRepository(Tile::class)->findBy([
            'type' => 'island',
            ]);

        //One random island
        $IslandRandom = array_rand($islands,1);

        return $islands[$IslandRandom];
    }

    public function treasureIsland()
    {
        $em = $this->entityManager;
        $treasure = $em->getRepository(Tile::class)->findOneBy([
            'hasTreasure' => 1,
        ]);

        return $treasure;

    }

    public function checkTreasure(Boat $boat)
    {
        $em = $this->entityManager;

        $tileCheck = $em->getRepository(Tile::class)->findOneBy([
            'coordX' => $boat->getCoordX(),
            'coordY' => $boat->getCoordY(),
            'hasTreasure' => 1,
        ]);

        if ($tileCheck) {
            return true;
        }
        return false;
    }

}