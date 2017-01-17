<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ClientlyCity;
use AppBundle\Form\ClientlyCityType;

/**
 * ClientlyCity controller.
 */
class ClientlyCityController extends Controller
{
    /**
     * Lists all ClientlyCity entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientlyCities = $em->getRepository('AppBundle:ClientlyCity')->findAll();

        return $this->render(
            'clientlycity/index.html.twig', array(
            'clientlyCities' => $clientlyCities,
            )
        );
    }

    /**
     * Creates a new ClientlyCity entity.
     */
    public function newAction(Request $request)
    {
        $clientlyCity = new ClientlyCity();
        $form = $this->createForm('AdminBundle\Form\ClientlyCityType', $clientlyCity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyCity);
            $em->flush();

            return $this->redirectToRoute('clientlycity_show', array('id' => $clientlyCity->getId()));
        }

        return $this->render(
            'clientlycity/new.html.twig', array(
            'clientlyCity' => $clientlyCity,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ClientlyCity entity.
     */
    public function showAction(ClientlyCity $clientlyCity)
    {
        $deleteForm = $this->createDeleteForm($clientlyCity);

        return $this->render(
            'clientlycity/show.html.twig', array(
            'clientlyCity' => $clientlyCity,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ClientlyCity entity.
     */
    public function editAction(Request $request, ClientlyCity $clientlyCity)
    {
        $deleteForm = $this->createDeleteForm($clientlyCity);
        $editForm = $this->createForm('AdminBundle\Form\ClientlyCityType', $clientlyCity);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyCity);
            $em->flush();

            return $this->redirectToRoute('clientlycity_edit', array('id' => $clientlyCity->getId()));
        }

        return $this->render(
            'clientlycity/edit.html.twig', array(
            'clientlyCity' => $clientlyCity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ClientlyCity entity.
     */
    public function deleteAction(Request $request, ClientlyCity $clientlyCity)
    {
        $form = $this->createDeleteForm($clientlyCity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($clientlyCity);
            $em->flush();
        }

        return $this->redirectToRoute('clientlycity_index');
    }

    /**
     * Creates a form to delete a ClientlyCity entity.
     *
     * @param ClientlyCity $clientlyCity The ClientlyCity entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientlyCity $clientlyCity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clientlycity_delete', array('id' => $clientlyCity->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
