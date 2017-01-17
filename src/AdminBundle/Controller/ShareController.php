<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Share;
use AppBundle\Form\ShareType;

/**
 * Share controller.
 */
class ShareController extends Controller
{
    /**
     * Lists all Share entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $shares = $em->getRepository('AppBundle:Share')->findAll();

        return $this->render(
            'share/index.html.twig', array(
            'shares' => $shares,
            )
        );
    }

    /**
     * Creates a new Share entity.
     */
    public function newAction(Request $request)
    {
        $share = new Share();
        $form = $this->createForm('AdminBundle\Form\ShareType', $share);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($share);
            $em->flush();

            return $this->redirectToRoute('share_show', array('id' => $share->getId()));
        }

        return $this->render(
            'share/new.html.twig', array(
            'share' => $share,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Share entity.
     */
    public function showAction(Share $share)
    {
        $deleteForm = $this->createDeleteForm($share);

        return $this->render(
            'share/show.html.twig', array(
            'share' => $share,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Share entity.
     */
    public function editAction(Request $request, Share $share)
    {
        $deleteForm = $this->createDeleteForm($share);
        $editForm = $this->createForm('AdminBundle\Form\ShareType', $share);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($share);
            $em->flush();

            return $this->redirectToRoute('share_edit', array('id' => $share->getId()));
        }

        return $this->render(
            'share/edit.html.twig', array(
            'share' => $share,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Share entity.
     */
    public function deleteAction(Request $request, Share $share)
    {
        $form = $this->createDeleteForm($share);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($share);
            $em->flush();
        }

        return $this->redirectToRoute('share_index');
    }

    /**
     * Creates a form to delete a Share entity.
     *
     * @param Share $share The Share entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Share $share)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('share_delete', array('id' => $share->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
