<?php

namespace Gtb\Bundle\CoreBundle\DateReservation;

use Doctrine\ORM\EntityManagerInterface;
use Gtb\Bundle\CoreBundle\Entity\Person;
use Gtb\Bundle\CoreBundle\Entity\Restaurant;
use Gtb\Bundle\CoreBundle\Entity\Reservation;
use Gtb\Bundle\ApiBundle\Exception\PersonNotAvailableException;
use Gtb\Bundle\ApiBundle\Exception\RestaurantNotAvailableException;

/**
 * Contains helper methods related to Reservations on specific dates.
 * Use this class to process Reservation data related to Restaurant and Person entities.
 *
 * @package Gtb\Bundle\CoreBundle\DateReservation
 */
class DateReservationUtils
{
    protected $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * @param Reservation $reservation
     * @throws RestaurantNotAvailableException
     * @throws PersonNotAvailableException
     */
    public function checkAvailability(Reservation $reservation)
    {
        // Check person availability
        $found = $this->em->getRepository('GtbCoreBundle:Reservation')->findOneBy(array(
                'person' => $reservation->getPerson(),
                'date' => $reservation->getDate()
            ));

        if ($found) { // a reservation for that person+date exists
            if (is_null($reservation->getId()) || // $reservation is new
                $reservation->getId() && $reservation->getId() != $found->getId()) { // $reservation exists and it is not the one $found
                throw new PersonNotAvailableException("Person already has a reservation on that date");
            }
        }

        // Check restaurant availability
        $restaurant = $this->em->getRepository('GtbCoreBundle:Restaurant')->find($reservation->getRestaurant()->getId());

        if ($this->isRestaurantFullOn($restaurant, $reservation->getDate())) {
            throw new RestaurantNotAvailableException("Restaurant has reached its maximum capacity on that date");
        }
    }

    /**
     * Checks if a $restaurant has reached max capacity (is full) on the specified date
     * @param Restaurant $restaurant
     * @param \DateTime $date
     * @return bool
     */
    public function isRestaurantFullOn(Restaurant $restaurant, \DateTime $date)
    {
        return count($this->getRestaurantReservationsFor($restaurant, $date)) < $restaurant->getMaxCapacity() ? false : true;
    }

    /**
     * Returns an array with Reservation instances for $restaurant on $date
     * @param Restaurant $restaurant
     * @param \DateTime $date
     * @return array
     */
    public function getRestaurantReservationsFor(Restaurant $restaurant, \DateTime $date)
    {
        $reservations = [];
        foreach ($restaurant->getReservations() as $reservation) {
            if ($this->daysMatch($reservation->getDate(), $date)) {
                $reservations[] = $reservation;
            }
        }

        return $reservations;
    }

    /**
     * Gets a Reservation instance for $person on $date.
     * Returns null if no reservation is found.
     * @param Person $person
     * @param \DateTime $date
     * @return mixed
     */
    public function getReservationFor(Person $person, \DateTime $date)
    {
        foreach ($person->getReservations() as $reservation) {
            if ($this->daysMatch($reservation->getDate(), $date)) {

                return $reservation;
            }
        }

        return null;
    }

    /**
     * Checks if the _day_ matches on two DateTime instances.
     * @param \DateTime $dateA
     * @param \DateTime $dateB
     * @return bool
     */
    public function daysMatch(\DateTime $dateA, \DateTime $dateB)
    {
        if ($dateA->diff($dateB)->days === 0 && !($dateB < $dateA)) {

            return true;
        }

        return false;
    }
}
