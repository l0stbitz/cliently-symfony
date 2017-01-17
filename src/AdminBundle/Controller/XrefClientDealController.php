<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\XrefClientDeal;
use AppBundle\Form\XrefClientDealType;

/**
 * XrefClientDeal controller.
 */
class XrefClientDealController extends Controller
{
    /**
     * Lists all XrefClientDeal entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $xrefClientDeals = $em->getRepository('AppBundle:XrefClientDeal')->findAll();

        return $this->render(
            'xrefclientdeal/index.html.twig', array(
            'xrefClientDeals' => $xrefClientDeals,
            )
        );
    }

    /**
     * Creates a new XrefClientDeal entity.
     */
    public function newAction(Request $request)
    {
        $xrefClientDeal = new XrefClientDeal();
        $form = $this->createForm('AdminBundle\Form\XrefClientDealType', $xrefClientDeal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($xrefClientDeal);
            $em->flush();

            return $this->redirectToRoute('xrefclientdeal_show', array('id' => $xrefClientDeal->getId()));
        }

        return $this->render(
            'xrefclientdeal/new.html.twig', array(
            'xrefClientDeal' => $xrefClientDeal,
            'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a XrefClientDeal entity.
     */
    public function showAction(XrefClientDeal $xrefClientDeal)
    {
        $deleteForm = $this->createDeleteForm($xrefClientDeal);

        return $this->render(
            'xrefclientdeal/show.html.twig', array(
            'xrefClientDeal' => $xrefClientDeal,
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing XrefClientDeal entity.
     */
    public function editAction(Request $request, XrefClientDeal $xrefClientDeal)
    {
        $deleteForm = $this->createDeleteForm($xrefClientDeal);
        $editForm = $this->createForm('AdminBundle\Form\XrefClientDealType', $xrefClientDeal);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($xrefClientDeal);
            $em->flush();

            return $this->redirectToRoute('xrefclientdeal_edit', array('id' => $xrefClientDeal->getId()));
        }

        return $this->render(
            'xrefclientdeal/edit.html.twig', array(
            'xrefClientDeal' => $xrefClientDeal,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a XrefClientDeal entity.
     */
    public function deleteAction(Request $request, XrefClientDeal $xrefClientDeal)
    {
        $form = $this->createDeleteForm($xrefClientDeal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($xrefClientDeal);
            $em->flush();
        }

        return $this->redirectToRoute('xrefclientdeal_index');
    }

    /**
     * Creates a form to delete a XrefClientDeal entity.
     *
     * @param XrefClientDeal $xrefClientDeal The XrefClientDeal entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(XrefClientDeal $xrefClientDeal)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('xrefclientdeal_delete', array('id' => $xrefClientDeal->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
