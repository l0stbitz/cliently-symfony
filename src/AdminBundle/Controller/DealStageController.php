<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\DealStage;
use AppBundle\Form\DealStageType;

/**
 * DealStage controller.
 */
class DealStageController extends Controller
{
    /**
     * Lists all DealStage entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dealStages = $em->getRepository('AppBundle:DealStage')->findAll();

        return $this->render(
            'dealstage/index.html.twig', array(
            'dealStages' => $dealStages,
            )
        );
    }

    /**
     * Creates a new DealStage entity.
     */
    public function newAction(Request $request)
    {
        $dealStage = new DealStage();
        $form = $this->createForm('AdminBundle\Form\DealStageType', $dealStage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dealStage);
            $em->flush();

            return $this->redirectToRoute('dealstage_show', array('id' => $dealStage->getId()));
        }

        return $this->render(
            'dealstage/new.html.twig', array(
            'dealStage' => $dealStage,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a DealStage entity.
     */
    public function showAction(DealStage $dealStage)
    {
        $deleteForm = $this->createDeleteForm($dealStage);

        return $this->render(
            'dealstage/show.html.twig', array(
            'dealStage' => $dealStage,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing DealStage entity.
     */
    public function editAction(Request $request, DealStage $dealStage)
    {
        $deleteForm = $this->createDeleteForm($dealStage);
        $editForm = $this->createForm('AdminBundle\Form\DealStageType', $dealStage);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dealStage);
            $em->flush();

            return $this->redirectToRoute('dealstage_edit', array('id' => $dealStage->getId()));
        }

        return $this->render(
            'dealstage/edit.html.twig', array(
            'dealStage' => $dealStage,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a DealStage entity.
     */
    public function deleteAction(Request $request, DealStage $dealStage)
    {
        $form = $this->createDeleteForm($dealStage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($dealStage);
            $em->flush();
        }

        return $this->redirectToRoute('dealstage_index');
    }

    /**
     * Creates a form to delete a DealStage entity.
     *
     * @param DealStage $dealStage The DealStage entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DealStage $dealStage)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dealstage_delete', array('id' => $dealStage->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
