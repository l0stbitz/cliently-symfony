<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Deal;
use AppBundle\Form\DealType;

/**
 * Deal controller.
 */
class DealController extends Controller
{
    /**
     * Lists all Deal entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $deals = $em->getRepository('AppBundle:Deal')->findAll();

        return $this->render(
            'deal/index.html.twig', array(
            'deals' => $deals,
            )
        );
    }

    /**
     * Creates a new Deal entity.
     */
    public function newAction(Request $request)
    {
        $deal = new Deal();
        $form = $this->createForm('AdminBundle\Form\DealType', $deal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($deal);
            $em->flush();

            return $this->redirectToRoute('deal_show', array('id' => $deal->getId()));
        }

        return $this->render(
            'deal/new.html.twig', array(
            'deal' => $deal,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Deal entity.
     */
    public function showAction(Deal $deal)
    {
        $deleteForm = $this->createDeleteForm($deal);

        return $this->render(
            'deal/show.html.twig', array(
            'deal' => $deal,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Deal entity.
     */
    public function editAction(Request $request, Deal $deal)
    {
        $deleteForm = $this->createDeleteForm($deal);
        $editForm = $this->createForm('AdminBundle\Form\DealType', $deal);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($deal);
            $em->flush();

            return $this->redirectToRoute('deal_edit', array('id' => $deal->getId()));
        }

        return $this->render(
            'deal/edit.html.twig', array(
            'deal' => $deal,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Deal entity.
     */
    public function deleteAction(Request $request, Deal $deal)
    {
        $form = $this->createDeleteForm($deal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($deal);
            $em->flush();
        }

        return $this->redirectToRoute('deal_index');
    }

    /**
     * Creates a form to delete a Deal entity.
     *
     * @param Deal $deal The Deal entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Deal $deal)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('deal_delete', array('id' => $deal->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
