<?php

namespace ERunner\Bundle\FinBackEndBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Gtb\Bundle\CoreBundle\Entity\Reservation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * LoadRestaurantData.
 *
 * @author Alejandro Bustamante <alejandro.bustamante.serrano@gmail.com>
 */
class LoadReservationData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $dateUtils = $this->container->get('gtb.core.date_reservation_utils');
        for ($i = 1 ; $i <= 5 ; ++$i) {
            $restaurant = $this->getReference("Restaurant-$i");
            for ($j = 1 ; $j <= 25 ; ++$j) {
                $person = $this->getReference("Person-$j");
                for ($k = 0 ; $k < 3 ; ++$k) {
                    $date = new \DateTime("+$k days");
                    if (!$dateUtils->isRestaurantFullOn($restaurant, $date) &&
                        !$dateUtils->getReservationFor($person, $date)) {
                            $reservation = new Reservation();
                            $reservation->setDate($date);
                            $reservation->setPerson($person);
                            $reservation->setRestaurant($restaurant);
                            $restaurant->addReservation($reservation);

                            $manager->persist($reservation);
                            $manager->persist($restaurant);
                    }
                }
            }
        }

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 3;
    }
}
