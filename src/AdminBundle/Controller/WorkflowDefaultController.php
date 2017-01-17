<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\WorkflowDefault;
use AppBundle\Form\WorkflowDefaultType;

/**
 * WorkflowDefault controller.
 */
class WorkflowDefaultController extends Controller
{
    /**
     * Lists all WorkflowDefault entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $workflowDefaults = $em->getRepository('AppBundle:WorkflowDefault')->findAll();

        return $this->render(
            'workflowdefault/index.html.twig', array(
            'workflowDefaults' => $workflowDefaults,
            )
        );
    }

    /**
     * Creates a new WorkflowDefault entity.
     */
    public function newAction(Request $request)
    {
        $workflowDefault = new WorkflowDefault();
        $form = $this->createForm('AdminBundle\Form\WorkflowDefaultType', $workflowDefault);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($workflowDefault);
            $em->flush();

            return $this->redirectToRoute('workflowdefault_show', array('id' => $workflowDefault->getId()));
        }

        return $this->render(
            'workflowdefault/new.html.twig', array(
            'workflowDefault' => $workflowDefault,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a WorkflowDefault entity.
     */
    public function showAction(WorkflowDefault $workflowDefault)
    {
        $deleteForm = $this->createDeleteForm($workflowDefault);

        return $this->render(
            'workflowdefault/show.html.twig', array(
            'workflowDefault' => $workflowDefault,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing WorkflowDefault entity.
     */
    public function editAction(Request $request, WorkflowDefault $workflowDefault)
    {
        $deleteForm = $this->createDeleteForm($workflowDefault);
        $editForm = $this->createForm('AdminBundle\Form\WorkflowDefaultType', $workflowDefault);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($workflowDefault);
            $em->flush();

            return $this->redirectToRoute('workflowdefault_edit', array('id' => $workflowDefault->getId()));
        }

        return $this->render(
            'workflowdefault/edit.html.twig', array(
            'workflowDefault' => $workflowDefault,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a WorkflowDefault entity.
     */
    public function deleteAction(Request $request, WorkflowDefault $workflowDefault)
    {
        $form = $this->createDeleteForm($workflowDefault);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($workflowDefault);
            $em->flush();
        }

        return $this->redirectToRoute('workflowdefault_index');
    }

    /**
     * Creates a form to delete a WorkflowDefault entity.
     *
     * @param WorkflowDefault $workflowDefault The WorkflowDefault entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(WorkflowDefault $workflowDefault)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('workflowdefault_delete', array('id' => $workflowDefault->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
