<?php

namespace ERunner\Bundle\FinBackEndBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Gtb\Bundle\CoreBundle\Entity\Restaurant;

/**
 * LoadRestaurantData.
 *
 * @author Alejandro Bustamante <alejandro.bustamante.serrano@gmail.com>
 */
class LoadRestaurantData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($x = 1 ; $x <= 5 ; ++$x) {

            $restaurant = new Restaurant();
            $restaurant->setName("Restaurant $x");
            $restaurant->setMaxCapacity($x * 2 + $x);

            $manager->persist($restaurant);

            $this->setReference(sprintf('Restaurant-%s', $x), $restaurant);
        }

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}
