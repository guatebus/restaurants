<?php

namespace ERunner\Bundle\FinBackEndBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Gtb\Bundle\CoreBundle\Entity\Reservation;

/**
 * LoadRestaurantData.
 *
 * @author Alejandro Bustamante <alejandro.bustamante.serrano@gmail.com>
 */
class LoadReservationData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1 ; $i <= 5 ; ++$i) {
            $restaurant = $this->getReference("Restaurant-$i");
            for ($j = 1 ; $j <= 25 ; ++$j) {
                $person = $this->getReference("Person-$j");
                for ($k = 0 ; $k < 3 ; ++$k) {
                    $date = new \DateTime("+$k days");
                    if (!$restaurant->isFullOn($date) && !$person->hasReservationOn($date)) {
                        $reservation = new Reservation();
                        $reservation->setDate($date);
                        $reservation->setPerson($person);
                        $reservation->setRestaurant($restaurant);

                        $manager->persist($reservation);
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
