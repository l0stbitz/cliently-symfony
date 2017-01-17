<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\DealLog;
use AppBundle\Form\DealLogType;

/**
 * DealLog controller.
 */
class DealLogController extends Controller
{
    /**
     * Lists all DealLog entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dealLogs = $em->getRepository('AppBundle:DealLog')->findAll();

        return $this->render(
            'deallog/index.html.twig', array(
            'dealLogs' => $dealLogs,
            )
        );
    }

    /**
     * Creates a new DealLog entity.
     */
    public function newAction(Request $request)
    {
        $dealLog = new DealLog();
        $form = $this->createForm('AdminBundle\Form\DealLogType', $dealLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dealLog);
            $em->flush();

            return $this->redirectToRoute('deallog_show', array('id' => $dealLog->getId()));
        }

        return $this->render(
            'deallog/new.html.twig', array(
            'dealLog' => $dealLog,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a DealLog entity.
     */
    public function showAction(DealLog $dealLog)
    {
        $deleteForm = $this->createDeleteForm($dealLog);

        return $this->render(
            'deallog/show.html.twig', array(
            'dealLog' => $dealLog,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing DealLog entity.
     */
    public function editAction(Request $request, DealLog $dealLog)
    {
        $deleteForm = $this->createDeleteForm($dealLog);
        $editForm = $this->createForm('AdminBundle\Form\DealLogType', $dealLog);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dealLog);
            $em->flush();

            return $this->redirectToRoute('deallog_edit', array('id' => $dealLog->getId()));
        }

        return $this->render(
            'deallog/edit.html.twig', array(
            'dealLog' => $dealLog,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a DealLog entity.
     */
    public function deleteAction(Request $request, DealLog $dealLog)
    {
        $form = $this->createDeleteForm($dealLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($dealLog);
            $em->flush();
        }

        return $this->redirectToRoute('deallog_index');
    }

    /**
     * Creates a form to delete a DealLog entity.
     *
     * @param DealLog $dealLog The DealLog entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DealLog $dealLog)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('deallog_delete', array('id' => $dealLog->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
