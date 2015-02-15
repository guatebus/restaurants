<?php

namespace Gtb\Bundle\CoreBundle\Tests\DateReservation;

use Doctrine\Common\Collections\ArrayCollection;
use Gtb\Bundle\CoreBundle\DateReservation\DateReservationUtils;
use Gtb\Bundle\CoreBundle\Entity\Reservation;
use Gtb\Bundle\CoreBundle\Entity\Restaurant;
use Gtb\Bundle\CoreBundle\Entity\Person;

class DateReservationUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @code
     * phpunit -v --filter testDaysMatch -c app/ src/Gtb/Bundle/CoreBundle/Tests/DateReservation/DateReservationUtilsTest.php
     * @endcode
     */
    public function testDaysMatch()
    {
        $dateReservationUtils = new DateReservationUtils();
        $dateStr = 'today';
        $date = new \DateTime($dateStr);

        for ($i = 1; $i <= 23; $i++) {
            $modified = new \DateTime($dateStr);
            $this->assertTrue($dateReservationUtils->daysMatch($date, $modified->modify("+ $i hours")));
        }

        $modified = new \DateTime($dateStr);
        $this->assertFalse($dateReservationUtils->daysMatch($date, $modified->modify("- 1 hours")));

        $modified = new \DateTime($dateStr);
        $this->assertFalse($dateReservationUtils->daysMatch($date, $modified->modify("+ 24 hours")));
    }

    /**
     * @code
     * phpunit -v --filter testIsRestaurantFullOn -c app/ src/Gtb/Bundle/CoreBundle/Tests/DateReservation/DateReservationUtilsTest.php
     * @endcode
     */
    public function testIsRestaurantFullOn()
    {
        $dateReservationUtils = new DateReservationUtils();
        $restaurant = new Restaurant();
        $restaurant->setMaxCapacity(2);
        $date = new \DateTime('now');

        $this->assertFalse($dateReservationUtils->isRestaurantFullOn($restaurant, $date));

        $person1 = new Person();

        $reservation = new Reservation();
        $reservation->setRestaurant($restaurant);
        $reservation->setPerson($person1);
        $reservation->setDate($date);

        $this->assertFalse($dateReservationUtils->isRestaurantFullOn($restaurant, $date));

        $person2 = new Person();
        $reservation = new Reservation();
        $reservation->setRestaurant($restaurant);
        $reservation->setPerson($person2);
        $reservation->setDate($date);

        $this->assertTrue($dateReservationUtils->isRestaurantFullOn($restaurant, $date));
    }

    /**
     * @code
     * phpunit -v --filter testGetReservationForUsingMockObjects -c app/ src/Gtb/Bundle/CoreBundle/Tests/DateReservation/DateReservationUtilsTest.php
     * @endcode
     */
    public function testGetReservationForUsingMockObjects()
    {
        $dateReservationUtils = new DateReservationUtils();
        $date = new \DateTime('now');

        $reservationMock = $this->getMock('Gtb\Bundle\CoreBundle\Entity\Reservation');
        $reservationMock->expects($this->exactly(2))
            ->method('getDate')
            ->will($this->returnValue($date));

        $personMock = $this->getMock('Gtb\Bundle\CoreBundle\Entity\Person');
        $personMock->expects($this->exactly(2))
            ->method('getReservations')
            ->will($this->returnValue(new ArrayCollection(array($reservationMock))));

        $this->assertSame($reservationMock, $dateReservationUtils->getReservationFor($personMock, $date));

        $this->assertNull($dateReservationUtils->getReservationFor($personMock, new \DateTime('now - 1 days')));
    }
}