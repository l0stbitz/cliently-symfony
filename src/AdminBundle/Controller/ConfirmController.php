<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Confirm;
use AppBundle\Form\ConfirmType;

/**
 * Confirm controller.
 */
class ConfirmController extends Controller
{
    /**
     * Lists all Confirm entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $confirms = $em->getRepository('AppBundle:Confirm')->findAll();

        return $this->render(
            'confirm/index.html.twig', array(
            'confirms' => $confirms,
            )
        );
    }

    /**
     * Creates a new Confirm entity.
     */
    public function newAction(Request $request)
    {
        $confirm = new Confirm();
        $form = $this->createForm('AdminBundle\Form\ConfirmType', $confirm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($confirm);
            $em->flush();

            return $this->redirectToRoute('confirm_show', array('id' => $confirm->getId()));
        }

        return $this->render(
            'confirm/new.html.twig', array(
            'confirm' => $confirm,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Confirm entity.
     */
    public function showAction(Confirm $confirm)
    {
        $deleteForm = $this->createDeleteForm($confirm);

        return $this->render(
            'confirm/show.html.twig', array(
            'confirm' => $confirm,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Confirm entity.
     */
    public function editAction(Request $request, Confirm $confirm)
    {
        $deleteForm = $this->createDeleteForm($confirm);
        $editForm = $this->createForm('AdminBundle\Form\ConfirmType', $confirm);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($confirm);
            $em->flush();

            return $this->redirectToRoute('confirm_edit', array('id' => $confirm->getId()));
        }

        return $this->render(
            'confirm/edit.html.twig', array(
            'confirm' => $confirm,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Confirm entity.
     */
    public function deleteAction(Request $request, Confirm $confirm)
    {
        $form = $this->createDeleteForm($confirm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($confirm);
            $em->flush();
        }

        return $this->redirectToRoute('confirm_index');
    }

    /**
     * Creates a form to delete a Confirm entity.
     *
     * @param Confirm $confirm The Confirm entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Confirm $confirm)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('confirm_delete', array('id' => $confirm->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
