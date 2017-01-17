<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ClientlySeniority;
use AppBundle\Form\ClientlySeniorityType;

/**
 * ClientlySeniority controller.
 */
class ClientlySeniorityController extends Controller
{
    /**
     * Lists all ClientlySeniority entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientlySeniorities = $em->getRepository('AppBundle:ClientlySeniority')->findAll();

        return $this->render(
            'clientlyseniority/index.html.twig', array(
            'clientlySeniorities' => $clientlySeniorities,
            )
        );
    }

    /**
     * Creates a new ClientlySeniority entity.
     */
    public function newAction(Request $request)
    {
        $clientlySeniority = new ClientlySeniority();
        $form = $this->createForm('AdminBundle\Form\ClientlySeniorityType', $clientlySeniority);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlySeniority);
            $em->flush();

            return $this->redirectToRoute('clientlyseniority_show', array('id' => $clientlySeniority->getId()));
        }

        return $this->render(
            'clientlyseniority/new.html.twig', array(
            'clientlySeniority' => $clientlySeniority,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ClientlySeniority entity.
     */
    public function showAction(ClientlySeniority $clientlySeniority)
    {
        $deleteForm = $this->createDeleteForm($clientlySeniority);

        return $this->render(
            'clientlyseniority/show.html.twig', array(
            'clientlySeniority' => $clientlySeniority,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ClientlySeniority entity.
     */
    public function editAction(Request $request, ClientlySeniority $clientlySeniority)
    {
        $deleteForm = $this->createDeleteForm($clientlySeniority);
        $editForm = $this->createForm('AdminBundle\Form\ClientlySeniorityType', $clientlySeniority);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlySeniority);
            $em->flush();

            return $this->redirectToRoute('clientlyseniority_edit', array('id' => $clientlySeniority->getId()));
        }

        return $this->render(
            'clientlyseniority/edit.html.twig', array(
            'clientlySeniority' => $clientlySeniority,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ClientlySeniority entity.
     */
    public function deleteAction(Request $request, ClientlySeniority $clientlySeniority)
    {
        $form = $this->createDeleteForm($clientlySeniority);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($clientlySeniority);
            $em->flush();
        }

        return $this->redirectToRoute('clientlyseniority_index');
    }

    /**
     * Creates a form to delete a ClientlySeniority entity.
     *
     * @param ClientlySeniority $clientlySeniority The ClientlySeniority entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientlySeniority $clientlySeniority)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clientlyseniority_delete', array('id' => $clientlySeniority->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
