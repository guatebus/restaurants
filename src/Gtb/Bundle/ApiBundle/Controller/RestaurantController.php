<?php

namespace Gtb\Bundle\ApiBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Util\Codes as ResponseCodes;
use Gtb\Bundle\CoreBundle\Entity\Restaurant;
use Gtb\Bundle\CoreBundle\Form\RestaurantType;
use Symfony\Component\HttpFoundation\Request;

class RestaurantController extends FOSRestController
{
    /**
     * Fetches a collection of Restaurant entities
     *
     * @param Request $request
     * @return array
     *
     * @Rest\Get("/restaurants")
     * @Rest\View
     */
    public function getRestaurantsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GtbCoreBundle:Restaurant')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Fetches a Restaurant entity
     *
     * @param Request $request
     * @param $id
     * @return array
     *
     * @Rest\Get("/restaurants/{id}")
     * @Rest\View
     */
    public function getRestaurantAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Creates a new Restaurant entity
     *
     * @param Request $request
     * @return array|\FOS\RestBundle\View\View
     *
     * @Rest\Post("/restaurants")
     */
    public function postRestaurantAction(Request $request)
    {
        $entity = new Restaurant();
        $form = $this->createForm(new RestaurantType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->transactional(function ($em) use ($entity) {
                    /* @var EntityManagerInterface $em */
                    $em->persist($entity);
                }
            );

            return $this->view($entity, ResponseCodes::HTTP_CREATED);
        }

        return array(
            'form' => $form,
        );
    }

    /**
     * Updates a Restaurant entity
     *
     * @param Request $request
     * @param $id
     * @return array|\FOS\RestBundle\View\View
     *
     * @Rest\Put("/restaurants/{id}")
     * @Rest\View
     */
    public function putRestaurantAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);
        $form = $this->createForm(new RestaurantType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
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
     * @Rest\Delete("/restaurants/{id}")
     */
    public function deleteRestaurantAction(Request $request, $id)
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

        $entity = $em->getRepository('GtbCoreBundle:Restaurant')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Restaurant entity');
        }

        return $entity;
    }
}
