<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Boat;
use AppBundle\Entity\Tile;
use AppBundle\Service\MapManager;
use AppBundle\Traits\BoatTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        return $this->render('map/index.html.twig', [
            'map'  => $map ?? [],
            'boat' => $boat,
            'tile' => $em->getRepository(Tile::class)->findOneBy([
                'coordX' => $boat->getCoordX(),
                'coordY' => $boat->getCoordY(),
            ])
        ]);
    }

    /**
     * @Route("/start", name="start")
     */
    public function startAction(MapManager $mapManager)
    {
        // reset the boat position (to O,O)
        //------------------------------------
        $this->getBoat()->setCoordX(0)->setCoordY(0);

        // remove the treasure from the database
        //---------------------------------------
        $tileWithTreasure = $this->getDoctrine()->getRepository(Tile::class)->findOneBy([
            'hasTreasure' => true,
        ]);

        if ($tileWithTreasure) {
            $tileWithTreasure->setHasTreasure(false);
        }

        // add the treasure to a random island
        //--------------------------------------
        $mapManager->getRandomIsland()->setHasTreasure(true);

        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('map');
    }
}
