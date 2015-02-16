<?php

namespace Gtb\Bundle\ApiBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Util\Codes as ResponseCodes;
use Gtb\Bundle\CoreBundle\Entity\Person;
use Gtb\Bundle\CoreBundle\Form\PersonType;
use Symfony\Component\HttpFoundation\Request;

class PersonController extends FOSRestController
{
    /**
     * Fetches a collection of Person entities
     *
     * @param Request $request
     * @return array
     *
     * @Rest\Get("/persons")
     * @Rest\View(serializerGroups={"list.persons", "list.reservations", "list.restaurants"})
     */
    public function getPersonsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GtbCoreBundle:Person')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Fetches a Person entity
     *
     * @param Request $request
     * @param $id
     * @return array
     *
     * @Rest\Get("/persons/{id}")
     * @Rest\View(serializerGroups={"details.persons", "list.reservations", "list.restaurants"})
     */
    public function getPersonAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Creates a new Person entity
     *
     * @param Request $request
     * @return array|\FOS\RestBundle\View\View
     *
     * @Rest\Post("/persons")
     */
    public function postPersonAction(Request $request)
    {
        $entity = new Person();
        $form = $this->createForm(new PersonType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
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
     * Updates a Person entity
     *
     * @param Request $request
     * @param $id
     * @return array|\FOS\RestBundle\View\View
     *
     * @Rest\Put("/persons/{id}")
     * @Rest\View
     */
    public function putPersonAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);
        $form = $this->createForm(new PersonType(), $entity);
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
     * @Rest\Delete("/persons/{id}")
     */
    public function deletePersonAction(Request $request, $id)
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

        $entity = $em->getRepository('GtbCoreBundle:Person')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Person entity');
        }

        return $entity;
    }
}
