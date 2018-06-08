<?php
namespace AppBundle\Services;


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

}