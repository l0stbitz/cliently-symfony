<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ClientTwitter;
use AppBundle\Form\ClientTwitterType;

/**
 * ClientTwitter controller.
 */
class ClientTwitterController extends Controller
{
    /**
     * Lists all ClientTwitter entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientTwitters = $em->getRepository('AppBundle:ClientTwitter')->findAll();

        return $this->render(
            'clienttwitter/index.html.twig', array(
            'clientTwitters' => $clientTwitters,
            )
        );
    }

    /**
     * Creates a new ClientTwitter entity.
     */
    public function newAction(Request $request)
    {
        $clientTwitter = new ClientTwitter();
        $form = $this->createForm('AppBundle\Form\ClientTwitterType', $clientTwitter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientTwitter);
            $em->flush();

            return $this->redirectToRoute('clienttwitter_show', array('id' => $clientTwitter->getId()));
        }

        return $this->render(
            'clienttwitter/new.html.twig', array(
            'clientTwitter' => $clientTwitter,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ClientTwitter entity.
     */
    public function showAction(ClientTwitter $clientTwitter)
    {
        $deleteForm = $this->createDeleteForm($clientTwitter);

        return $this->render(
            'clienttwitter/show.html.twig', array(
            'clientTwitter' => $clientTwitter,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ClientTwitter entity.
     */
    public function editAction(Request $request, ClientTwitter $clientTwitter)
    {
        $deleteForm = $this->createDeleteForm($clientTwitter);
        $editForm = $this->createForm('AppBundle\Form\ClientTwitterType', $clientTwitter);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientTwitter);
            $em->flush();

            return $this->redirectToRoute('clienttwitter_edit', array('id' => $clientTwitter->getId()));
        }

        return $this->render(
            'clienttwitter/edit.html.twig', array(
            'clientTwitter' => $clientTwitter,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ClientTwitter entity.
     */
    public function deleteAction(Request $request, ClientTwitter $clientTwitter)
    {
        $form = $this->createDeleteForm($clientTwitter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($clientTwitter);
            $em->flush();
        }

        return $this->redirectToRoute('clienttwitter_index');
    }

    /**
     * Creates a form to delete a ClientTwitter entity.
     *
     * @param ClientTwitter $clientTwitter The ClientTwitter entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientTwitter $clientTwitter)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clienttwitter_delete', array('id' => $clientTwitter->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
