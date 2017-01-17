<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\StageDefault;
use AppBundle\Form\StageDefaultType;

/**
 * StageDefault controller.
 */
class StageDefaultController extends Controller
{
    /**
     * Lists all StageDefault entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stageDefaults = $em->getRepository('AppBundle:StageDefault')->findAll();

        return $this->render(
            'stagedefault/index.html.twig', array(
            'stageDefaults' => $stageDefaults,
            )
        );
    }

    /**
     * Creates a new StageDefault entity.
     */
    public function newAction(Request $request)
    {
        $stageDefault = new StageDefault();
        $form = $this->createForm('AdminBundle\Form\StageDefaultType', $stageDefault);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stageDefault);
            $em->flush();

            return $this->redirectToRoute('stagedefault_show', array('id' => $stageDefault->getId()));
        }

        return $this->render(
            'stagedefault/new.html.twig', array(
            'stageDefault' => $stageDefault,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a StageDefault entity.
     */
    public function showAction(StageDefault $stageDefault)
    {
        $deleteForm = $this->createDeleteForm($stageDefault);

        return $this->render(
            'stagedefault/show.html.twig', array(
            'stageDefault' => $stageDefault,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing StageDefault entity.
     */
    public function editAction(Request $request, StageDefault $stageDefault)
    {
        $deleteForm = $this->createDeleteForm($stageDefault);
        $editForm = $this->createForm('AdminBundle\Form\StageDefaultType', $stageDefault);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stageDefault);
            $em->flush();

            return $this->redirectToRoute('stagedefault_edit', array('id' => $stageDefault->getId()));
        }

        return $this->render(
            'stagedefault/edit.html.twig', array(
            'stageDefault' => $stageDefault,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a StageDefault entity.
     */
    public function deleteAction(Request $request, StageDefault $stageDefault)
    {
        $form = $this->createDeleteForm($stageDefault);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stageDefault);
            $em->flush();
        }

        return $this->redirectToRoute('stagedefault_index');
    }

    /**
     * Creates a form to delete a StageDefault entity.
     *
     * @param StageDefault $stageDefault The StageDefault entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StageDefault $stageDefault)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stagedefault_delete', array('id' => $stageDefault->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
