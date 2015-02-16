<?php

namespace Gtb\Bundle\ApiBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Util\Codes as ResponseCodes;
use Gtb\Bundle\CoreBundle\Entity\Reservation;
use Gtb\Bundle\CoreBundle\Form\ReservationType;
use Symfony\Component\HttpFoundation\Request;

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
        $qb = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('GtbCoreBundle:Reservation')
            ->createQueryBuilder('r')
        ;

        if ($request->query->get('y') &&
            $request->query->get('m') &&
            $request->query->get('d')) {
            $date = new \DateTime(sprintf('%d-%d-%d',
                $request->query->get('y'),
                $request->query->get('m'),
                $request->query->get('d')
            ));
            $qb->andWhere($qb->expr()->between('r.date', ':date_from', ':date_to'));
            $qb->setParameter('date_from', $date, \Doctrine\DBAL\Types\Type::DATETIME);
            $qb->setParameter('date_to', $date, \Doctrine\DBAL\Types\Type::DATETIME);

            // strict date comparison works when there isn't a date range being queried for
            // left here for reference (as no commented code should reach production!)
            //$qb->andWhere('r.date = :date')->setParameter('date', $date, \Doctrine\DBAL\Types\Type::DATETIME);
        }

        if ($restaurantId = $request->query->get('rid')) {
            $qb->andWhere('r.restaurant = :rid')->setParameter('rid', $restaurantId);
        }

        if ($personId = $request->query->get('pid')) {
            $qb->andWhere('r.person = :pid')->setParameter('pid', $personId);
        }

        return array(
            'entities' => $qb->getQuery()->getResult(),
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
            $this->get('gtb.core.date_reservation_utils')->checkAvailability($entity);

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
            $this->get('gtb.core.date_reservation_utils')->checkAvailability($entity);

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
