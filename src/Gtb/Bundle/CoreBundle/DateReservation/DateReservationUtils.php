<?php

namespace Gtb\Bundle\CoreBundle\DateReservation;

use Gtb\Bundle\CoreBundle\Entity\Person;
use Gtb\Bundle\CoreBundle\Entity\Restaurant;

/**
 * Contains helper methods related to Reservations on specific dates.
 * Use this class to process Reservation data related to Restaurant and Person entities.
 *
 * @package Gtb\Bundle\CoreBundle\DateReservation
 */
class DateReservationUtils
{

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
