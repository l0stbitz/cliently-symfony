<?php

namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\DealWorkflow;
use AppBundle\Form\DealWorkflowType;

/**
 * DealWorkflow controller.
 */
class DealWorkflowController extends Controller
{
    /**
     * Lists all DealWorkflow entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dealWorkflows = $em->getRepository('AppBundle:DealWorkflow')->findAll();

        return $this->render(
            'dealworkflow/index.html.twig', array(
            'dealWorkflows' => $dealWorkflows,
            )
        );
    }

    /**
     * Creates a new DealWorkflow entity.
     */
    public function newAction(Request $request)
    {
        $dealWorkflow = new DealWorkflow();
        $form = $this->createForm('AppBundle\Form\DealWorkflowType', $dealWorkflow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dealWorkflow);
            $em->flush();

            return $this->redirectToRoute('dealworkflow_show', array('id' => $dealWorkflow->getId()));
        }

        return $this->render(
            'dealworkflow/new.html.twig', array(
            'dealWorkflow' => $dealWorkflow,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a DealWorkflow entity.
     */
    public function showAction(DealWorkflow $dealWorkflow)
    {
        $deleteForm = $this->createDeleteForm($dealWorkflow);

        return $this->render(
            'dealworkflow/show.html.twig', array(
            'dealWorkflow' => $dealWorkflow,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing DealWorkflow entity.
     */
    public function editAction(Request $request, DealWorkflow $dealWorkflow)
    {
        $deleteForm = $this->createDeleteForm($dealWorkflow);
        $editForm = $this->createForm('AppBundle\Form\DealWorkflowType', $dealWorkflow);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dealWorkflow);
            $em->flush();

            return $this->redirectToRoute('dealworkflow_edit', array('id' => $dealWorkflow->getId()));
        }

        return $this->render(
            'dealworkflow/edit.html.twig', array(
            'dealWorkflow' => $dealWorkflow,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a DealWorkflow entity.
     */
    public function deleteAction(Request $request, DealWorkflow $dealWorkflow)
    {
        $form = $this->createDeleteForm($dealWorkflow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($dealWorkflow);
            $em->flush();
        }

        return $this->redirectToRoute('dealworkflow_index');
    }

    /**
     * Creates a form to delete a DealWorkflow entity.
     *
     * @param DealWorkflow $dealWorkflow The DealWorkflow entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DealWorkflow $dealWorkflow)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dealworkflow_delete', array('id' => $dealWorkflow->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
