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
        //Initialisation boat position to 0,0
        $boat = $this->getBoat();
        $boat->setCoordX(0);
        $boat->setCoordY(0);

        //remove treasure
        $treasure = $mapManager->treasureIsland();

        if($treasure) {
            $treasure->setHasTreasure(false);
        }

        //add new treasure
        $mapManager->getRandomIsland()->setHasTreasure(true);

        $this->getDoctrine()->getManager()->flush();

        //redirection map
        return $this->redirectToRoute('map');
    }
}
