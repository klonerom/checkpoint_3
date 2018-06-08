<?php
/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 25/05/18
 * Time: 11:26
 */

namespace AppBundle\Traits;

use AppBundle\Entity\Boat;
use Doctrine\ORM\EntityManagerInterface;

trait BoatTrait
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return Boat
     */
    public function getBoat()
    {
        return $this->em->getRepository(Boat::class)->findOneBy([]);
    }
}
