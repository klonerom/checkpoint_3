<?php

namespace AppBundle\Service;

use AppBundle\Entity\Boat;
use AppBundle\Entity\Tile;
use AppBundle\Repository\TileRepository;
use Doctrine\ORM\EntityManagerInterface;

class MapManager
{
    /**
     * @var TileRepository
     */
    private $tileRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->tileRepository = $em->getRepository(Tile::class);
    }

    public function tileExists(int $x, int $y) : bool
    {
        // the tile exist if there is one in database...
        return (bool) $this->tileRepository->findOneBy([
            'coordX' => $x,
            'coordY' => $y,
        ]);
    }

    /**
     * @return Tile a tile of type island (we assume there is always one ;))
     */
    public function getRandomIsland() : Tile
    {
        $islands = $this->tileRepository->findBy(['type' => 'island']);

        $randomKey = array_rand($islands);

        return $islands[$randomKey];
    }

    /**
     * Is the boat on the tile with the treasure ?
     * @param Boat $boat
     * @return bool
     */
    public function checkTreasure(Boat $boat)
    {
        $tileWithTreasure = $this->tileRepository->findOneBy([
            'hasTreasure' => true,
        ]);

        if (!$tileWithTreasure) {
            return false;
        }

        return
            $boat->getCoordX() == $tileWithTreasure->getCoordX() &&
            $boat->getCoordY() == $tileWithTreasure->getCoordY();
    }
}