<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ClientlyRevenue;
use AppBundle\Form\ClientlyRevenueType;

/**
 * ClientlyRevenue controller.
 */
class ClientlyRevenueController extends Controller
{
    /**
     * Lists all ClientlyRevenue entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientlyRevenues = $em->getRepository('AppBundle:ClientlyRevenue')->findAll();

        return $this->render(
            'clientlyrevenue/index.html.twig', array(
            'clientlyRevenues' => $clientlyRevenues,
            )
        );
    }

    /**
     * Creates a new ClientlyRevenue entity.
     */
    public function newAction(Request $request)
    {
        $clientlyRevenue = new ClientlyRevenue();
        $form = $this->createForm('AdminBundle\Form\ClientlyRevenueType', $clientlyRevenue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyRevenue);
            $em->flush();

            return $this->redirectToRoute('clientlyrevenue_show', array('id' => $clientlyRevenue->getId()));
        }

        return $this->render(
            'clientlyrevenue/new.html.twig', array(
            'clientlyRevenue' => $clientlyRevenue,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ClientlyRevenue entity.
     */
    public function showAction(ClientlyRevenue $clientlyRevenue)
    {
        $deleteForm = $this->createDeleteForm($clientlyRevenue);

        return $this->render(
            'clientlyrevenue/show.html.twig', array(
            'clientlyRevenue' => $clientlyRevenue,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ClientlyRevenue entity.
     */
    public function editAction(Request $request, ClientlyRevenue $clientlyRevenue)
    {
        $deleteForm = $this->createDeleteForm($clientlyRevenue);
        $editForm = $this->createForm('AdminBundle\Form\ClientlyRevenueType', $clientlyRevenue);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyRevenue);
            $em->flush();

            return $this->redirectToRoute('clientlyrevenue_edit', array('id' => $clientlyRevenue->getId()));
        }

        return $this->render(
            'clientlyrevenue/edit.html.twig', array(
            'clientlyRevenue' => $clientlyRevenue,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ClientlyRevenue entity.
     */
    public function deleteAction(Request $request, ClientlyRevenue $clientlyRevenue)
    {
        $form = $this->createDeleteForm($clientlyRevenue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($clientlyRevenue);
            $em->flush();
        }

        return $this->redirectToRoute('clientlyrevenue_index');
    }

    /**
     * Creates a form to delete a ClientlyRevenue entity.
     *
     * @param ClientlyRevenue $clientlyRevenue The ClientlyRevenue entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientlyRevenue $clientlyRevenue)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clientlyrevenue_delete', array('id' => $clientlyRevenue->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
