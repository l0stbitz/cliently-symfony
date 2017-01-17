<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ClientlyRoles;
use AppBundle\Form\ClientlyRolesType;

/**
 * ClientlyRoles controller.
 */
class ClientlyRolesController extends Controller
{
    /**
     * Lists all ClientlyRoles entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientlyRoles = $em->getRepository('AppBundle:ClientlyRoles')->findAll();

        return $this->render(
            'clientlyroles/index.html.twig', array(
            'clientlyRoles' => $clientlyRoles,
            )
        );
    }

    /**
     * Creates a new ClientlyRoles entity.
     */
    public function newAction(Request $request)
    {
        $clientlyRole = new ClientlyRoles();
        $form = $this->createForm('AdminBundle\Form\ClientlyRolesType', $clientlyRole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyRole);
            $em->flush();

            return $this->redirectToRoute('clientlyroles_show', array('id' => $clientlyRole->getId()));
        }

        return $this->render(
            'clientlyroles/new.html.twig', array(
            'clientlyRole' => $clientlyRole,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ClientlyRoles entity.
     */
    public function showAction(ClientlyRoles $clientlyRole)
    {
        $deleteForm = $this->createDeleteForm($clientlyRole);

        return $this->render(
            'clientlyroles/show.html.twig', array(
            'clientlyRole' => $clientlyRole,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ClientlyRoles entity.
     */
    public function editAction(Request $request, ClientlyRoles $clientlyRole)
    {
        $deleteForm = $this->createDeleteForm($clientlyRole);
        $editForm = $this->createForm('AdminBundle\Form\ClientlyRolesType', $clientlyRole);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyRole);
            $em->flush();

            return $this->redirectToRoute('clientlyroles_edit', array('id' => $clientlyRole->getId()));
        }

        return $this->render(
            'clientlyroles/edit.html.twig', array(
            'clientlyRole' => $clientlyRole,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ClientlyRoles entity.
     */
    public function deleteAction(Request $request, ClientlyRoles $clientlyRole)
    {
        $form = $this->createDeleteForm($clientlyRole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($clientlyRole);
            $em->flush();
        }

        return $this->redirectToRoute('clientlyroles_index');
    }

    /**
     * Creates a form to delete a ClientlyRoles entity.
     *
     * @param ClientlyRoles $clientlyRole The ClientlyRoles entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientlyRoles $clientlyRole)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clientlyroles_delete', array('id' => $clientlyRole->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
