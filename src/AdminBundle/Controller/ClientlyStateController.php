<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\ClientlyState;
use AppBundle\Form\ClientlyStateType;

/**
 * ClientlyState controller.
 */
class ClientlyStateController extends Controller
{
    /**
     * Lists all ClientlyState entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clientlyStates = $em->getRepository('AppBundle:ClientlyState')->findAll();

        return $this->render(
            'clientlystate/index.html.twig', array(
            'clientlyStates' => $clientlyStates,
            )
        );
    }

    /**
     * Creates a new ClientlyState entity.
     */
    public function newAction(Request $request)
    {
        $clientlyState = new ClientlyState();
        $form = $this->createForm('AdminBundle\Form\ClientlyStateType', $clientlyState);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyState);
            $em->flush();

            return $this->redirectToRoute('clientlystate_show', array('id' => $clientlyState->getId()));
        }

        return $this->render(
            'clientlystate/new.html.twig', array(
            'clientlyState' => $clientlyState,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ClientlyState entity.
     */
    public function showAction(ClientlyState $clientlyState)
    {
        $deleteForm = $this->createDeleteForm($clientlyState);

        return $this->render(
            'clientlystate/show.html.twig', array(
            'clientlyState' => $clientlyState,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing ClientlyState entity.
     */
    public function editAction(Request $request, ClientlyState $clientlyState)
    {
        $deleteForm = $this->createDeleteForm($clientlyState);
        $editForm = $this->createForm('AdminBundle\Form\ClientlyStateType', $clientlyState);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientlyState);
            $em->flush();

            return $this->redirectToRoute('clientlystate_edit', array('id' => $clientlyState->getId()));
        }

        return $this->render(
            'clientlystate/edit.html.twig', array(
            'clientlyState' => $clientlyState,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ClientlyState entity.
     */
    public function deleteAction(Request $request, ClientlyState $clientlyState)
    {
        $form = $this->createDeleteForm($clientlyState);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($clientlyState);
            $em->flush();
        }

        return $this->redirectToRoute('clientlystate_index');
    }

    /**
     * Creates a form to delete a ClientlyState entity.
     *
     * @param ClientlyState $clientlyState The ClientlyState entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientlyState $clientlyState)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clientlystate_delete', array('id' => $clientlyState->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
