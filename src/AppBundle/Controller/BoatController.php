<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Boat;
use AppBundle\Services\MapManager;
use AppBundle\Traits\BoatTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Boat controller.
 *
 * @Route("boat")
 */
class BoatController extends Controller
{
    use BoatTrait;

    /**
     * Move the boat to coord x,y
     * @Route("/move/{x}/{y}", name="moveBoat", requirements={"x"="\d+", "y"="\d+"}))
     */
    public function moveBoatAction(int $x, int $y)
    {
        $em = $this->getDoctrine()->getManager();
        $boat = $this->getBoat();

        $boat->setCoordX($x);
        $boat->setCoordY($y);

        $em->flush();

        return $this->redirectToRoute('map');
    }

    /**
     * take a direction as parameter. This direction can only be 'N', 'S', 'E' or 'W'
     * @Route("/move/{compass}", name="moveDirection", requirements={"compass"="N|S|E|W"})
     * @Method({"GET", "POST"})
     */
    public function moveDirectionAction($compass, MapManager $mapManager)
    {
        $em = $this->getDoctrine()->getManager();
        $boat = $this->getBoat();

        $x = $boat->getCoordX();
        $y = $boat->getCoordY();

        if ($compass == 'N') {
            $y -= 1;

        } elseif ($compass == 'S') {
            $y += 1;

        } elseif ($compass == 'E') {
            $x += 1;

        } elseif ($compass == 'W') {
            $x -= 1;
        }

        $mapPosition = $mapManager->tileExists($x, $y);

        if ($mapPosition == false) {
            $this->addFlash('danger','Are you Crazy Jack ?!!! Your are going to out of map ! JohnDoDev Pirate is going to kill you !');
            return $this->redirectToRoute('map');
        }

        $this->moveBoatAction($x, $y);

        $treasure = $mapManager->checkTreasure($boat);
        if ($treasure) {
            $this->addFlash('success','Jack you find the treasure ! Congratulations !!!');
        }

        return $this->redirectToRoute('map');

    }

    /**
     * Lists all boat entities.
     *
     * @Route("/", name="boat_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $boats = $em->getRepository('AppBundle:Boat')->findAll();

        return $this->render('boat/index.html.twig', array(
            'boats' => $boats,
        ));
    }

    /**
     * Creates a new boat entity.
     *
     * @Route("/new", name="boat_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $boat = new Boat();
        $form = $this->createForm('AppBundle\Form\BoatType', $boat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($boat);
            $em->flush();

            return $this->redirectToRoute('boat_show', array('id' => $boat->getId()));
        }

        return $this->render('boat/new.html.twig', array(
            'boat' => $boat,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a boat entity.
     *
     * @Route("/{id}", name="boat_show")
     * @Method("GET")
     */
    public function showAction(Boat $boat)
    {
        $deleteForm = $this->createDeleteForm($boat);

        return $this->render('boat/show.html.twig', array(
            'boat'        => $boat,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a boat entity.
     *
     * @param Boat $boat The boat entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Boat $boat)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('boat_delete', array('id' => $boat->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing boat entity.
     *
     * @Route("/{id}/edit", name="boat_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Boat $boat)
    {
        $deleteForm = $this->createDeleteForm($boat);
        $editForm = $this->createForm('AppBundle\Form\BoatType', $boat);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('boat_edit', array('id' => $boat->getId()));
        }

        return $this->render('boat/edit.html.twig', array(
            'boat'        => $boat,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a boat entity.
     *
     * @Route("/{id}", name="boat_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Boat $boat)
    {
        $form = $this->createDeleteForm($boat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($boat);
            $em->flush();
        }

        return $this->redirectToRoute('boat_index');
    }
}
