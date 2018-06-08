<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Boat;
use AppBundle\Entity\Tile;
use AppBundle\Services\MapManager;
use AppBundle\Traits\BoatTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class MapController extends Controller
{
    use BoatTrait;

    /**
     * @Route("/map", name="map")
     */
    public function displayMapAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tiles = $em->getRepository(Tile::class)->findAll();

        foreach ($tiles as $tile) {
            $map[$tile->getCoordX()][$tile->getCoordY()] = $tile;
        }

        $boat = $this->getBoat();

        $tileBoat = $em->getRepository(Tile::class)->findOneBy([
            'coordX' => $boat->getCoordX(),
            'coordY' => $boat->getCoordY(),
        ]);

        return $this->render('map/index.html.twig', [
            'map'  => $map ?? [],
            'boat' => $boat,
            'tileBoat' => $tileBoat,
        ]);
    }

    /**
     * @Route("/start", name="start")
     */
    public function startAction(MapManager $mapManager)
    {
        $em = $this->getDoctrine()->getManager();

        //Initialisation boat position to 0,0
        $boat = $this->getBoat();
        $boat->setCoordX(0);
        $boat->setCoordY(0);

        $em->persist($boat);
        $em->flush();

        //remove treasure
        $treasure = $mapManager->treasureIsland();

        if($treasure) {
            $treasure->setHasTreasure(null);
            $em->persist($treasure);
            $em->flush();
        }


        //add new treasure
        $treasureIsland = $mapManager->getRandomIsland();
        $tileTreasure = $em->getRepository(Tile::class)->findOneBy([
            'coordX' => $treasureIsland->getCoordX(),
            'coordY' => $treasureIsland->getCoordY(),
        ]);
        $tileTreasure->setHasTreasure(true);
        $em->persist($tileTreasure);
        $em->flush();

        //redirection map
        return $this->redirectToRoute('map');
    }
}
