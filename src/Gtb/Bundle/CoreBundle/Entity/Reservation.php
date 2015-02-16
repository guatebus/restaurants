<?php

namespace Gtb\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Reservation
 */
class Reservation
{
    /**
     * @var integer
     *
     * @Serializer\Groups({"list.reservations", "details.reservations"})
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @Serializer\Groups({"list.reservations", "details.reservations"})
     */
    private $date;

    /**
     * @var Restaurant
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Groups({"list.reservations", "details.reservations"})
     */
    private $restaurant;

    /**
     * @var Person
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Groups({"list.reservations", "details.reservations"})
     */
    private $person;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Reservation
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param $restaurant
     * @return $this
     */
    public function setRestaurant($restaurant)
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    /**
     * @return Restaurant
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * @param $person
     * @return $this
     */
    public function setPerson($person)
    {
        $this->person = $person;

        $person->addReservation($this);

        return $this;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

}
