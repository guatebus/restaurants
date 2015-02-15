<?php

namespace Gtb\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Restaurant
 */
class Restaurant
{
    /**
     * @var integer
     * @Serializer\Groups({"list.restaurants", "details.restaurants"})
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Groups({"list.restaurants", "details.restaurants"})
     */
    private $name;

    /**
     * @var integer
     *
     * @Assert\Type(type="integer")
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "This value should be {{ limit }} or more"
     * )
     *
     * @Serializer\Groups({"list.restaurants", "details.restaurants"})
     */
    private $maxCapacity;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @Serializer\Groups({"list.restaurants", "details.restaurants"})
     */
    private $reservations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     * @return Restaurant
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set maxCapacity
     *
     * @param integer $maxCapacity
     * @return Restaurant
     */
    public function setMaxCapacity($maxCapacity)
    {
        $this->maxCapacity = $maxCapacity;

        return $this;
    }

    /**
     * Get maxCapacity
     *
     * @return integer 
     */
    public function getMaxCapacity()
    {
        return $this->maxCapacity;
    }

    /**
     * Add reservation
     *
     * @param  Reservation $reservation
     * @return Person
     */
    public function addReservation(Reservation $reservation)
    {
        $this->reservations[] = $reservation;

        return $this;
    }

    /**
     * Remove reservation
     *
     * @param Reservation $reservation
     */
    public function removeReservation(Reservation $reservation)
    {
        $this->reservations->removeElement($reservation);
    }

    /**
     * Get reservations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReservations()
    {
        return $this->reservations;
    }

}
