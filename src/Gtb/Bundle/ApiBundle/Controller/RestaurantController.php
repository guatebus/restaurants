<?php

namespace Gtb\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Util\Codes as ResponseCodes;
use Gtb\Bundle\CoreBundle\Entity\Restaurant;
use Gtb\Bundle\CoreBundle\Form\RestaurantType;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;

class RestaurantController extends FOSRestController
{
    /**
     * Fetches a collection of Restaurant entities
     *
     * @param Request $request
     * @return array
     *
     * @Get("/restaurants")
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
     * @Get("/restaurants/{id}")
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
     * @Post("/restaurants")
     */
    public function postRestaurantAction(Request $request)
    {
        $entity = new Restaurant();
        $form = $this->createForm(new RestaurantType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

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
     * @Put("/restaurants/{id}")
     * @Rest\View
     */
    public function putRestaurantAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);
        $form = $this->createForm(new RestaurantType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

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
     * @Delete("/restaurants/{id}")
     */
    public function deleteRestaurantAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

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
