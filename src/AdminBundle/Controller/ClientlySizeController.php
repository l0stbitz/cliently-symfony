<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ClientlySize;
use AppBundle\Form\ClientlySizeType;

/**
 * ClientlySize controller.
 */
class ClientlySizeController extends Controller
{
    /**
     * Lists all ClientlySize entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientlySizes = $em->getRepository('AppBundle:ClientlySize')->findAll();

        return $this->render(
            'clientlysize/index.html.twig', array(
            'clientlySizes' => $clientlySizes,
            )
        );
    }

    /**
     * Creates a new ClientlySize entity.
     */
    public function newAction(Request $request)
    {
        $clientlySize = new ClientlySize();
        $form = $this->createForm('AdminBundle\Form\ClientlySizeType', $clientlySize);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlySize);
            $em->flush();

            return $this->redirectToRoute('clientlysize_show', array('id' => $clientlySize->getId()));
        }

        return $this->render(
            'clientlysize/new.html.twig', array(
            'clientlySize' => $clientlySize,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ClientlySize entity.
     */
    public function showAction(ClientlySize $clientlySize)
    {
        $deleteForm = $this->createDeleteForm($clientlySize);

        return $this->render(
            'clientlysize/show.html.twig', array(
            'clientlySize' => $clientlySize,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ClientlySize entity.
     */
    public function editAction(Request $request, ClientlySize $clientlySize)
    {
        $deleteForm = $this->createDeleteForm($clientlySize);
        $editForm = $this->createForm('AdminBundle\Form\ClientlySizeType', $clientlySize);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlySize);
            $em->flush();

            return $this->redirectToRoute('clientlysize_edit', array('id' => $clientlySize->getId()));
        }

        return $this->render(
            'clientlysize/edit.html.twig', array(
            'clientlySize' => $clientlySize,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ClientlySize entity.
     */
    public function deleteAction(Request $request, ClientlySize $clientlySize)
    {
        $form = $this->createDeleteForm($clientlySize);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($clientlySize);
            $em->flush();
        }

        return $this->redirectToRoute('clientlysize_index');
    }

    /**
     * Creates a form to delete a ClientlySize entity.
     *
     * @param ClientlySize $clientlySize The ClientlySize entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientlySize $clientlySize)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clientlysize_delete', array('id' => $clientlySize->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
