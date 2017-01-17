<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\PipelineDefault;
use AppBundle\Form\PipelineDefaultType;

/**
 * PipelineDefault controller.
 */
class PipelineDefaultController extends Controller
{
    /**
     * Lists all PipelineDefault entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pipelineDefaults = $em->getRepository('AppBundle:PipelineDefault')->findAll();

        return $this->render(
            'pipelinedefault/index.html.twig', array(
            'pipelineDefaults' => $pipelineDefaults,
            )
        );
    }

    /**
     * Creates a new PipelineDefault entity.
     */
    public function newAction(Request $request)
    {
        $pipelineDefault = new PipelineDefault();
        $form = $this->createForm('AdminBundle\Form\PipelineDefaultType', $pipelineDefault);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pipelineDefault);
            $em->flush();

            return $this->redirectToRoute('pipelinedefault_show', array('id' => $pipelineDefault->getId()));
        }

        return $this->render(
            'pipelinedefault/new.html.twig', array(
            'pipelineDefault' => $pipelineDefault,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a PipelineDefault entity.
     */
    public function showAction(PipelineDefault $pipelineDefault)
    {
        $deleteForm = $this->createDeleteForm($pipelineDefault);

        return $this->render(
            'pipelinedefault/show.html.twig', array(
            'pipelineDefault' => $pipelineDefault,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing PipelineDefault entity.
     */
    public function editAction(Request $request, PipelineDefault $pipelineDefault)
    {
        $deleteForm = $this->createDeleteForm($pipelineDefault);
        $editForm = $this->createForm('AdminBundle\Form\PipelineDefaultType', $pipelineDefault);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pipelineDefault);
            $em->flush();

            return $this->redirectToRoute('pipelinedefault_edit', array('id' => $pipelineDefault->getId()));
        }

        return $this->render(
            'pipelinedefault/edit.html.twig', array(
            'pipelineDefault' => $pipelineDefault,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a PipelineDefault entity.
     */
    public function deleteAction(Request $request, PipelineDefault $pipelineDefault)
    {
        $form = $this->createDeleteForm($pipelineDefault);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pipelineDefault);
            $em->flush();
        }

        return $this->redirectToRoute('pipelinedefault_index');
    }

    /**
     * Creates a form to delete a PipelineDefault entity.
     *
     * @param PipelineDefault $pipelineDefault The PipelineDefault entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PipelineDefault $pipelineDefault)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pipelinedefault_delete', array('id' => $pipelineDefault->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
