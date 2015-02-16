<?php

namespace Gtb\Bundle\ApiBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Util\Codes as ResponseCodes;
use Gtb\Bundle\ApiBundle\Exception\PersonNotAvailableException;
use Gtb\Bundle\ApiBundle\Exception\RestaurantNotAvailableException;
use Gtb\Bundle\CoreBundle\Entity\Reservation;
use Gtb\Bundle\CoreBundle\Form\ReservationType;
use Symfony\Component\HttpFoundation\Request;
use Gtb\Bundle\CoreBundle\DateReservation\DateReservationUtils;

class ReservationController extends FOSRestController
{
    /**
     * Fetches a collection of Reservation entities
     *
     * @param Request $request
     * @return array
     *
     * @Rest\Get("/reservations")
     * @Rest\View(serializerGroups={"list.reservations", "list.persons", "list.restaurants"})
     */
    public function getReservationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GtbCoreBundle:Reservation')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Fetches a Reservation entity
     *
     * @param Request $request
     * @param $id
     * @return array
     *
     * @Rest\Get("/reservations/{id}")
     * @Rest\View(serializerGroups={"details.reservations", "list.persons", "list.restaurants"})
     */
    public function getReservationAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Creates a new Reservation entity
     *
     * @param Request $request
     * @return array|\FOS\RestBundle\View\View
     *
     * @Rest\Post("/reservations")
     * @Rest\View(serializerGroups={"details.reservations", "list.persons", "list.restaurants"})
     */
    public function postReservationAction(Request $request)
    {
        $entity = new Reservation();
        $form = $this->createForm(new ReservationType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->checkAvailability($entity);

            // reservation added to restaurant after availability check to prevent clashing during check
            $entity->getRestaurant()->addReservation($entity);

            $this->getDoctrine()->getManager()->transactional(function ($em) use ($entity) {
                    /* @var EntityManagerInterface $em */
                    $em->persist($entity);
                }
            );

            return $this->view(
                array(
                    'entity' => $entity,
                ),
                ResponseCodes::HTTP_CREATED
            );
        }

        return array(
            'form' => $form,
        );
    }

    /**
     * Updates a Reservation entity
     *
     * @param Request $request
     * @param $id
     * @return array|\FOS\RestBundle\View\View
     *
     * @Rest\Put("/reservations/{id}")
     * @Rest\View
     */
    public function putReservationAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);
        $form = $this->createForm(new ReservationType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $this->checkAvailability($entity);

            // reservation added to restaurant after availability check to prevent clashing during check
            $entity->getRestaurant()->addReservation($entity);

            $this->getDoctrine()->getManager()->transactional(function ($em) use ($entity) {
                    /* @var EntityManagerInterface $em */
                    $em->persist($entity);
                }
            );

            return array(
                'entity' => $entity,
            );
        }

        return array(
            'form' => $form,
        );
    }

    /**
     * @param Request $request
     * @param $id
     * @return \FOS\RestBundle\View\View
     *
     * @Rest\Delete("/reservations/{id}")
     */
    public function deleteReservationAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);

        $this->getDoctrine()->getManager()->transactional(function ($em) use ($entity) {
                /* @var EntityManagerInterface $em */
                $em->remove($entity);
            }
        );

        return $this->view(null, ResponseCodes::HTTP_NO_CONTENT);
    }

    /**
     * Checks if the person and restaurant in $reservation is available to book the reservation
     *
     * @param Reservation $reservation
     * @throws \Gtb\Bundle\ApiBundle\Exception\RestaurantNotAvailableException Restaurant not available
     * @throws \Gtb\Bundle\ApiBundle\Exception\PersonNotAvailableException Person not available
     */
    protected function checkAvailability(Reservation $reservation)
    {
        $em = $this->getDoctrine()->getManager();

        // Check person availability
        $found = $em->getRepository('GtbCoreBundle:Reservation')->findOneBy(array(
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

        // No need to check if $reservation exists as if it does, it was:
        // (1) either edited so the person or date changed (this check is handled by code above)
        // (2) the restaurant changed, so the code below will handle that check
        /* @var DateReservationUtils $dateReservationUtils */
        $dateReservationUtils = $this->get('gtb.core.date_reservation_utils');
        if ($dateReservationUtils->isRestaurantFullOn($reservation->getRestaurant(), $reservation->getDate())) {
            throw new RestaurantNotAvailableException("Restaurant has reached its maximum capacity on that date");
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GtbCoreBundle:Reservation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reservation entity');
        }

        return $entity;
    }
}
