<?php

namespace ERunner\Bundle\FinBackEndBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Gtb\Bundle\CoreBundle\Entity\Person;

/**
 * LoadRestaurantData.
 *
 * @author Alejandro Bustamante <alejandro.bustamante.serrano@gmail.com>
 */
class LoadPersonData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($x = 1 ; $x <= 25 ; ++$x) {

            $person = new Person();
            $person->setName("Person $x");

            $manager->persist($person);

            $this->setReference(sprintf('Person-%s', $x), $person);
        }

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }
}
